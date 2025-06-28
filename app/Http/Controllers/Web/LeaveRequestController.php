<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    /**
     * Display leave requests based on user role
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        // if (!$employee && !$user->isHRAdmin()) {
        //     return redirect()->route('dashboard')
        //         ->with('error', 'No employee record found for your account.');
        // }

        $query = LeaveRequest::with(['employee.department', 'employee.role', 'approver']);

        // Role-based filtering
        if ($user->isEmployee() && $employee) {
            $query->where('employee_id', $employee->id);
        } elseif ($user->isManager() && $employee) {
            $subordinateIds = $employee->subordinates->pluck('id')->toArray();
            $subordinateIds[] = $employee->id; // Include own requests
            $query->whereIn('employee_id', $subordinateIds);
        }

        // HR Admin sees all requests (no additional filtering)

        // Apply tab-specific filters
        $activeTab = $request->get('tab', 'my-requests');

        if ($activeTab === 'my-requests') {
            // Filter for personal requests
            if ($employee) {
                $query->where('employee_id', $employee->id);
            }

            // Apply my requests specific filters
            if ($request->has('my_status') && $request->my_status !== null) {
                $query->where('status', $request->my_status);
            }

            if ($request->has('my_leave_type') && $request->my_leave_type !== null) {
                $query->where('leave_type', $request->my_leave_type);
            }

            if ($request->has('my_date_from') && $request->my_date_from !== null) {
                $query->where('start_date', '>=', $request->my_date_from);
            }

            if ($request->has('my_date_to') && $request->my_date_to !== null) {
                $query->where('end_date', '<=', $request->my_date_to);
            }
        } elseif ($activeTab === 'team-requests') {
            // Filter for team requests (exclude own requests for managers)

            if ($user->isManager() && $employee) {
                $subordinateIds = $employee->subordinates->pluck('id')->toArray();
                $query->whereIn('employee_id', $subordinateIds);
            } elseif ($user->isHRAdmin()) {
                // HR can see all, but exclude their own in team tab
                if ($employee) {
                    $query->where('employee_id', '!=', $employee->id);
                }
            }
            // Apply team requests specific filters
            if ($request->has('team_status') && $request->team_status !== null) {
                $query->where('status', $request->team_status);
            }

            if ($request->has('team_leave_type') && $request->team_leave_type !== null) {
                $query->where('leave_type', $request->team_leave_type);
            }

            if ($request->has('team_employee') && $request->team_employee !== null) {
                $query->where('employee_id', $request->team_employee);
            }

            if ($request->has('team_date_from') && $request->team_date_from !== null) {
                $query->where('start_date', '>=', $request->team_date_from);
            }

            if ($request->has('team_date_to') && $request->team_date_to !== null) {
                $query->where('end_date', '<=', $request->team_date_to);
            }
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->get();

        // Get employees for filter dropdown (based on role)
        $employees = collect();
        if ($user->isHRAdmin()) {
            $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        } elseif ($user->isManager() && $employee) {
            $employees = $employee->subordinates()->where('status', 'active')->orderBy('first_name')->get();
        }

        return view('leave-requests.index', compact('leaveRequests', 'employees', 'user'));
    }

    /**
     * Show the form for creating a new leave request
     */
    public function create()
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'No employee record found for your account.');
        }

        // HR can apply for any employee
        $employees = collect();
        if ($user->isHRAdmin()) {
            $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        }

        return view('leave-requests.create', compact('employees', 'user'));
    }

    /**
     * Store a newly created leave request
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee && !$user->isHRAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'No employee record found for your account.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => $user->isHRAdmin() ? 'required|exists:employees,id' : 'nullable',
            'leave_type' => 'required|in:' . implode(',', array_keys(LeaveRequest::LEAVE_TYPES)),
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $daysRequested = $startDate->diffInDays($endDate) + 1;

            // Determine which employee this request is for
            $requestEmployeeId = $user->isHRAdmin() ? $request->employee_id : $employee->id;
            $requestEmployee = Employee::find($requestEmployeeId);

            // Check for overlapping leave requests
            $overlapping = LeaveRequest::where('employee_id', $requestEmployeeId)
                ->where('status', '!=', 'rejected')
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })
                ->exists();

            if ($overlapping) {
                return redirect()->back()
                    ->with('error', 'Leave request overlaps with existing leave dates.')
                    ->withInput();
            }

            $leaveRequest = LeaveRequest::create([
                'employee_id' => $requestEmployeeId,
                'leave_type' => $request->leave_type,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'days_requested' => $daysRequested,
                'reason' => $request->reason,
                'status' => 'pending',
                'request_date' => now(),
            ]);

            DB::commit();

            $message = $user->isHRAdmin()
                ? "Leave request for {$requestEmployee->first_name} {$requestEmployee->last_name} has been submitted successfully!"
                : "Your leave request has been submitted successfully!";

            return redirect()->route('leave-requests.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to submit leave request. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified leave request
     */
    public function show(LeaveRequest $leaveRequest)
    {
        $user = auth()->user();
        $employee = $user->employee;

        // Check access permissions
        if (!$this->canViewLeaveRequest($leaveRequest, $user, $employee)) {
            return redirect()->route('leave-requests.index')
                ->with('error', 'You do not have permission to view this leave request.');
        }

        $leaveRequest->load(['employee.department', 'employee.role', 'approver']);

        return view('leave-requests.show', compact('leaveRequest', 'user'));
    }

    /**
     * Update leave request status (approve/reject)
     */
    public function updateStatus(Request $request, LeaveRequest $leaveRequest)
    {
        $user = auth()->user();
        $employee = $user->employee;

        // Check if user can approve/reject this request
        if (!$this->canApproveLeaveRequest($leaveRequest, $user, $employee)) {
            return redirect()->route('leave-requests.index')
                ->with('error', 'You do not have permission to approve/reject this request.');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'manager_comments' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            DB::beginTransaction();

            $leaveRequest->update([
                'status' => $request->status,
                'approved_by' => $employee->id,
                'manager_comments' => $request->manager_comments,
                'response_date' => now(),
            ]);

            DB::commit();

            $statusText = $request->status === 'approved' ? 'approved' : 'rejected';
            $message = "Leave request has been {$statusText} successfully!";

            return redirect()->route('leave-requests.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update leave request status. Please try again.');
        }
    }

    /**
     * Remove the specified leave request
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        $user = auth()->user();
        $employee = $user->employee;

        // Only allow deletion of own pending requests or HR admin
        if (
            !$user->isHRAdmin() &&
            ($leaveRequest->employee_id !== $employee->id || $leaveRequest->status !== 'pending')
        ) {
            return redirect()->route('leave-requests.index')
                ->with('error', 'You can only delete your own pending leave requests.');
        }

        try {
            $leaveRequest->delete();

            return redirect()->route('leave-requests.index')
                ->with('success', 'Leave request has been deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete leave request. Please try again.');
        }
    }

    /**
     * Check if user can view leave request
     */
    /**
     * Check if user can view leave request
     */
    private function canViewLeaveRequest(LeaveRequest $leaveRequest, $user, $employee)
    {
        if ($user->isHRAdmin()) {
            return true;
        }

        if ($user->isManager() && $employee) {
            $subordinateIds = $employee->subordinates->pluck('id')->toArray();
            $subordinateIds[] = $employee->id; // Include own requests for viewing
            return in_array($leaveRequest->employee_id, $subordinateIds);
        }

        // Employee can only view their own requests
        return $leaveRequest->employee_id === $employee->id;
    }

    /**
     * Check if user can approve/reject leave request
     */
    private function canApproveLeaveRequest(LeaveRequest $leaveRequest, $user, $employee)
    {
        // HR Admin can approve any request
        if ($user->isHRAdmin()) {
            return true;
        }

        // Managers can approve subordinates' requests but NOT their own
        if ($user->isManager() && $employee) {
            $subordinateIds = $employee->subordinates->pluck('id')->toArray();
            // IMPORTANT: Check that it's NOT their own request
            return in_array($leaveRequest->employee_id, $subordinateIds) &&
                $leaveRequest->employee_id !== $employee->id && // Cannot approve own request
                $leaveRequest->status === 'pending';
        }

        return false;
    }
}
