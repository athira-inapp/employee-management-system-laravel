<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DummyUsersEmployeesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Get existing departments and roles
        $departments = Department::all();
        $roles = Role::all();

        // If no departments/roles exist, create some basic ones
        if ($departments->isEmpty()) {
            $departments = collect([
                Department::create(['name' => 'Human Resources', 'status' => 'active']),
                Department::create(['name' => 'Information Technology', 'status' => 'active']),
                Department::create(['name' => 'Finance', 'status' => 'active']),
                Department::create(['name' => 'Marketing', 'status' => 'active']),
                Department::create(['name' => 'Operations', 'status' => 'active']),
            ]);
        }

        if ($roles->isEmpty()) {
            $roles = collect([
                Role::create(['name' => 'Software Developer', 'base_salary' => 65000]),
                Role::create(['name' => 'Senior Developer', 'base_salary' => 85000]),
                Role::create(['name' => 'Team Lead', 'base_salary' => 95000]),
                Role::create(['name' => 'HR Specialist', 'base_salary' => 55000]),
                Role::create(['name' => 'Accountant', 'base_salary' => 50000]),
                Role::create(['name' => 'Marketing Specialist', 'base_salary' => 45000]),
                Role::create(['name' => 'Operations Coordinator', 'base_salary' => 40000]),
            ]);
        }

        // Sample data arrays for realistic names
        $firstNames = [
            'John',
            'Jane',
            'Michael',
            'Sarah',
            'David',
            'Emily',
            'Robert',
            'Lisa',
            'James',
            'Maria',
            'William',
            'Jennifer',
            'Richard',
            'Linda',
            'Joseph',
            'Elizabeth',
            'Thomas',
            'Susan',
            'Christopher',
            'Jessica',
            'Daniel',
            'Ashley',
            'Matthew',
            'Kimberly',
            'Anthony',
            'Donna',
            'Mark',
            'Nancy',
            'Donald',
            'Betty',
            'Steven',
            'Helen',
            'Paul',
            'Sandra',
            'Andrew',
            'Dorothy',
            'Joshua',
            'Lisa',
            'Kenneth',
            'Ruth'
        ];

        $lastNames = [
            'Smith',
            'Johnson',
            'Williams',
            'Brown',
            'Jones',
            'Garcia',
            'Miller',
            'Davis',
            'Rodriguez',
            'Martinez',
            'Hernandez',
            'Lopez',
            'Gonzales',
            'Wilson',
            'Anderson',
            'Thomas',
            'Taylor',
            'Moore',
            'Jackson',
            'Martin',
            'Lee',
            'Perez',
            'Thompson',
            'White',
            'Harris',
            'Sanchez',
            'Clark',
            'Ramirez',
            'Lewis',
            'Robinson',
            'Walker',
            'Young',
            'Allen',
            'King',
            'Wright',
            'Scott',
            'Torres',
            'Nguyen',
            'Hill',
            'Flores'
        ];

        // Create 20 employees with users
        for ($i = 1; $i <= 20; $i++) {
            $firstName = $faker->randomElement($firstNames);
            $lastName = $faker->randomElement($lastNames);
            $email = strtolower($firstName . '.' . $lastName . $i . '@company.com');

            // Ensure unique email
            while (Employee::where('email', $email)->exists() || User::where('email', $email)->exists()) {
                $email = strtolower($firstName . '.' . $lastName . rand(100, 999) . '@company.com');
            }

            $department = $departments->random();
            $role = $roles->random();

            // Create salary based on role with some variation
            $baseSalary = $role->base_salary ?? 50000;
            $salary = $baseSalary + rand(-5000, 15000);

            // Create employee first
            $employee = Employee::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'hire_date' => $faker->dateTimeBetween('-3 years', '-1 month')->format('Y-m-d'),
                'salary' => $salary,
                'department_id' => $department->id,
                'role_id' => $role->id,
                'status' => 'active'
            ]);

            // Determine user role based on position and random assignment
            $userRole = $this->determineUserRole($role->name, $i);

            // Create corresponding user
            $user = User::create([
                'name' => $firstName . ' ' . $lastName,
                'email' => $email,
                'password' => Hash::make('password'), // Default password
                'employee_id' => $employee->id,
                'user_role' => $userRole,
                'is_active' => true,
                'email_verified_at' => now(),
                'last_login' => rand(0, 1) ? $faker->dateTimeBetween('-1 month', 'now') : null,
            ]);

            echo "Created: {$firstName} {$lastName} ({$email}) - {$userRole}\n";
        }

        // After creating all employees, assign some managers
        $this->assignManagers();

        echo "\n✅ Successfully created 20 employees with user accounts!\n";
        echo "📧 All users have the password: 'password'\n";
        echo "👥 User roles distributed across admin, manager, and employee\n";
    }

    /**
     * Determine user role based on position and index
     */
    private function determineUserRole($roleName, $index)
    {
        // Make some users admins (HR roles)
        if (stripos($roleName, 'hr') !== false || $index <= 2) {
            return 'admin';
        }

        // Make some users managers (Team leads and senior positions)
        if (
            stripos($roleName, 'lead') !== false ||
            stripos($roleName, 'senior') !== false ||
            ($index % 5 == 0 && $index > 2)
        ) {
            return 'manager';
        }

        // Rest are employees
        return 'employee';
    }

    /**
     * Assign managers to employees
     */
    private function assignManagers()
    {
        // Get all managers and employees
        $managers = Employee::whereHas('user', function ($query) {
            $query->whereIn('user_role', ['admin', 'manager']);
        })->get();

        $employees = Employee::whereHas('user', function ($query) {
            $query->where('user_role', 'employee');
        })->get();

        // Assign managers to employees (same department if possible)
        foreach ($employees as $employee) {
            // Try to find a manager in the same department
            $departmentManager = $managers->where('department_id', $employee->department_id)->first();

            if ($departmentManager && $departmentManager->id !== $employee->id) {
                $employee->update(['manager_id' => $departmentManager->id]);
                echo "Assigned {$departmentManager->first_name} {$departmentManager->last_name} as manager for {$employee->first_name} {$employee->last_name}\n";
            } else {
                // Assign any available manager
                $availableManager = $managers->where('id', '!=', $employee->id)->random();
                if ($availableManager) {
                    $employee->update(['manager_id' => $availableManager->id]);
                    echo "Assigned {$availableManager->first_name} {$availableManager->last_name} as manager for {$employee->first_name} {$employee->last_name}\n";
                }
            }
        }

        // Update departments with managers
        $departments = Department::all();
        foreach ($departments as $department) {
            $departmentManager = $managers->where('department_id', $department->id)->first();
            if ($departmentManager) {
                $department->update(['manager_id' => $departmentManager->user->id]);
                echo "Set {$departmentManager->first_name} {$departmentManager->last_name} as {$department->name} department manager\n";
            }
        }
    }
}
