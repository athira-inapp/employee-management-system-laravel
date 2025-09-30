<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\EmployeeCreated;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees
     * GET /api/employees
     */
    public function index(Request $request)
    {
        try {
            $query = Employee::with(['department', 'role', 'manager']);

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'ILIKE', "%{$search}%")
                        ->orWhere('last_name', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) ILIKE ?", ["%{$search}%"]);
                });
            }

            // Filter by department
            if ($request->has('department_id') && !empty($request->department_id)) {
                $query->where('department_id', $request->department_id);
            }

            // Filter by role
            if ($request->has('role_id') && !empty($request->role_id)) {
                $query->where('role_id', $request->role_id);
            }

            // Filter by status
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSorts = ['first_name', 'last_name', 'email', 'hire_date', 'salary', 'created_at'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $employees = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Employees retrieved successfully',
                'data' => [
                    'employees' => $employees->items(),
                    'pagination' => [
                        'current_page' => $employees->currentPage(),
                        'last_page' => $employees->lastPage(),
                        'per_page' => $employees->perPage(),
                        'total' => $employees->total(),
                        'from' => $employees->firstItem(),
                        'to' => $employees->lastItem(),
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve employees',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created employee
     * POST /api/employees
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|email|unique:employees,email|max:100',
                'phone' => 'nullable|string|max:15',
                'address' => 'nullable|string',
                'hire_date' => 'required|date',
                'salary' => 'nullable|numeric|min:0|max:999999.99',
                'department_id' => 'nullable|exists:departments,id',
                'role_id' => 'nullable|exists:roles,id',
                'manager_id' => 'nullable|exists:employees,id',
                'status' => 'required|in:active,inactive'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if manager exists and is in the same department (optional business rule)
            if ($request->manager_id && $request->department_id) {
                $manager = Employee::find($request->manager_id);
                if ($manager && $manager->department_id != $request->department_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Manager must be in the same department'
                    ], 422);
                }
            }

            DB::beginTransaction();

            $employee = Employee::create($validator->validated());

            // Load relationships for response
            $employee->load(['department', 'role', 'manager']);

            DB::commit();
            try {
                // Mail::to(env('ADMIN_EMAIL'))->send(new EmployeeCreated($employee));
                Mail::to(env('ADMIN_EMAIL'))->queue(new EmployeeCreated($employee));
                Log::info('Email queued for employee: ' . $employee->id);
                // Can also send to multiple recipients
                // Mail::to(env('ADMIN_EMAIL'))
                //     ->cc('manager@company.com')
                //     ->bcc('hr@company.com')
                //     ->send(new EmployeeCreated($employee));

            } catch (\Exception $e) {
                Log::error('Failed to queue email: ' . $e->getMessage());
                // Log::error('Email failed: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully',
                'data' => [
                    'employee' => $employee
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified employee
     * GET /api/employees/{id}
     */
    public function show($id)
    {
        try {
            $employee = Employee::with(['department', 'role', 'manager', 'subordinates'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Employee retrieved successfully',
                'data' => [
                    'employee' => $employee
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified employee
     * PUT/PATCH /api/employees/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'first_name' => 'sometimes|required|string|max:50',
                'last_name' => 'sometimes|required|string|max:50',
                'email' => 'sometimes|required|email|max:100|unique:employees,email,' . $id,
                'phone' => 'nullable|string|max:15',
                'address' => 'nullable|string',
                'hire_date' => 'sometimes|required|date',
                'salary' => 'nullable|numeric|min:0|max:999999.99',
                'department_id' => 'nullable|exists:departments,id',
                'role_id' => 'nullable|exists:roles,id',
                'manager_id' => 'nullable|exists:employees,id',
                'status' => 'sometimes|required|in:active,inactive'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Prevent self-assignment as manager
            if ($request->manager_id && $request->manager_id == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee cannot be their own manager'
                ], 422);
            }

            // Check if manager exists and is in the same department (optional business rule)
            if ($request->has('manager_id') && $request->manager_id && $request->has('department_id') && $request->department_id) {
                $manager = Employee::find($request->manager_id);
                if ($manager && $manager->department_id != $request->department_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Manager must be in the same department'
                    ], 422);
                }
            }

            DB::beginTransaction();

            $employee->update($validator->validated());

            // Load relationships for response
            $employee->load(['department', 'role', 'manager']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully',
                'data' => [
                    'employee' => $employee
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified employee
     * DELETE /api/employees/{id}
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);

            // Check if employee has subordinates
            $subordinatesCount = Employee::where('manager_id', $id)->count();
            if ($subordinatesCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete employee who manages other employees. Please reassign subordinates first.'
                ], 422);
            }

            // Check if employee has a user account
            $hasUser = \App\Models\User::where('employee_id', $id)->exists();
            if ($hasUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete employee who has a user account. Please deactivate the user account first.'
                ], 422);
            }

            DB::beginTransaction();

            $employeeName = $employee->first_name . ' ' . $employee->last_name;
            $employee->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Employee {$employeeName} deleted successfully"
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employees for dropdown/select options
     * GET /api/employees/options
     */
    public function options()
    {
        try {
            $employees = Employee::select('id', 'first_name', 'last_name', 'department_id')
                ->with('department:id,name')
                ->where('status', 'active')
                ->orderBy('first_name')
                ->get()
                ->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->first_name . ' ' . $employee->last_name,
                        'department' => $employee->department ? $employee->department->name : null
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Employee options retrieved successfully',
                'data' => [
                    'employees' => $employees
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve employee options',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get departments for dropdown
     * GET /api/employees/departments
     */
    public function getDepartments()
    {
        try {
            $departments = Department::select('id', 'name')
                ->where('status', 'active')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => ['departments' => $departments]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve departments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get roles for dropdown
     * GET /api/employees/roles
     */
    public function getRoles()
    {
        try {
            $roles = Role::select('id', 'name', 'base_salary')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => ['roles' => $roles]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
