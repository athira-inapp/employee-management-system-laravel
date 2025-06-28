<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees
     */
    public function index(Request $request)
    {
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
        if ($request->has('department') && !empty($request->department)) {
            $query->where('department_id', $request->department);
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(9);
        $departments = Department::where('status', 'active')->orderBy('name')->get();

        return view('employees.index', compact('employees', 'departments'));
    }

    /**
     * Show the form for creating a new employee
     */
    public function create()
    {
        $departments = Department::where('status', 'active')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $managers = Employee::where('status', 'active')->orderBy('first_name')->get();

        return view('employees.create', compact('departments', 'roles', 'managers'));
    }

    /**
     * Store a newly created employee
     */
    public function store(Request $request)
    {
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $employee = Employee::create($validator->validated());

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', "Employee {$employee->first_name} {$employee->last_name} has been added successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create employee. Please try again.')
                ->withInput();
        }
    }



    /**
     * Display the specified employee
     */
    public function show(Employee $employee)
    {
        $employee->load([
            'department',
            'role',
            'manager',
            'subordinates' => function ($query) {
                $query->with(['department', 'role'])->where('status', 'active');
            },
            'user'
        ]);

        return view('employees.show', compact('employee'));
    }

    /**
     * Remove the specified employee
     */
    public function destroy(Employee $employee)
    {
        try {
            // Check if employee has subordinates
            $subordinatesCount = Employee::where('manager_id', $employee->id)->count();
            if ($subordinatesCount > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete employee who manages other employees. Please reassign subordinates first.');
            }

            DB::beginTransaction();

            $employeeName = $employee->first_name . ' ' . $employee->last_name;
            $employee->delete();

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', "Employee {$employeeName} has been deleted successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete employee. Please try again.');
        }
    }


    /**
     * Show the form for editing the specified employee
     */
    public function edit(Employee $employee)
    {
        $departments = Department::where('status', 'active')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        // Get managers (exclude current employee to prevent self-assignment)
        $managers = Employee::where('status', 'active')
            ->where('id', '!=', $employee->id)
            ->orderBy('first_name')
            ->get();

        return view('employees.edit', compact('employee', 'departments', 'roles', 'managers'));
    }

    /**
     * Update the specified employee
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:employees,email,' . $employee->id,
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Additional business logic validation
        if ($request->manager_id == $employee->id) {
            return redirect()->back()
                ->with('error', 'Employee cannot be their own manager.')
                ->withInput();
        }

        // Check if the employee is trying to be assigned to their subordinate
        if ($request->manager_id) {
            $subordinateIds = $this->getAllSubordinateIds($employee->id);
            if (in_array($request->manager_id, $subordinateIds)) {
                return redirect()->back()
                    ->with('error', 'Cannot assign employee to their subordinate as manager.')
                    ->withInput();
            }
        }

        try {
            DB::beginTransaction();

            $oldData = [
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'email' => $employee->email,
                'department' => $employee->department ? $employee->department->name : null,
                'role' => $employee->role ? $employee->role->name : null
            ];

            $employee->update($validator->validated());

            // Load fresh data for comparison
            $employee->load(['department', 'role']);

            $newData = [
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'email' => $employee->email,
                'department' => $employee->department ? $employee->department->name : null,
                'role' => $employee->role ? $employee->role->name : null
            ];

            DB::commit();

            // Create success message with changes summary
            $changes = [];
            if ($oldData['name'] !== $newData['name']) {
                $changes[] = "name changed to {$newData['name']}";
            }
            if ($oldData['email'] !== $newData['email']) {
                $changes[] = "email updated";
            }
            if ($oldData['department'] !== $newData['department']) {
                $changes[] = "department changed to {$newData['department']}";
            }
            if ($oldData['role'] !== $newData['role']) {
                $changes[] = "role changed to {$newData['role']}";
            }

            $message = "Employee {$employee->first_name} {$employee->last_name} has been updated successfully!";
            if (!empty($changes)) {
                $message .= " Changes: " . implode(', ', $changes) . ".";
            }

            return redirect()->route('employees.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update employee. Please try again.')
                ->withInput();
        }
    }



    /**
     * Get all subordinate IDs recursively
     */
    private function getAllSubordinateIds($employeeId, $visited = [])
    {
        if (in_array($employeeId, $visited)) {
            return []; // Prevent infinite recursion
        }

        $visited[] = $employeeId;
        $subordinateIds = [];

        $directSubordinates = Employee::where('manager_id', $employeeId)->pluck('id')->toArray();

        foreach ($directSubordinates as $subordinateId) {
            $subordinateIds[] = $subordinateId;
            $nestedSubordinateIds = $this->getAllSubordinateIds($subordinateId, $visited);
            $subordinateIds = array_merge($subordinateIds, $nestedSubordinateIds);
        }

        return array_unique($subordinateIds);
    }
}
