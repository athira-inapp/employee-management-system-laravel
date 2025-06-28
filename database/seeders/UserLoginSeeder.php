<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserLoginSeeder extends Seeder
{
    public function run()
    {
        // // Create HR Employee first
        // $hrEmployee = Employee::create([
        //     'first_name' => 'Sarahs',
        //     'last_name' => 'Johnson',
        //     'email' => 'hrmanager@company.com',
        //     'phone' => '+1-555-0101',
        //     'address' => '123 Main St, City, State 12345',
        //     'hire_date' => '2022-01-15',
        //     'salary' => 75000.00,
        //     'department_id' => 1, // HR Department
        //     'role_id' => 1, // HR Manager role
        //     'status' => 'active'
        // ]);

        // // Create HR User linked to employee
        // User::create([
        //     'name' => 'Sarahs Johnson',
        //     'email' => 'hrmanager@company.com',
        //     'password' => Hash::make('password'),
        //     'employee_id' => $hrEmployee->id,
        //     'user_role' => 'admin',
        //     'is_active' => true,
        //     'email_verified_at' => now(),
        // ]);

        // Create Manager Employee
        // $managerEmployee = Employee::create([
        //     'first_name' => 'David',
        //     'last_name' => 'Chen',
        //     'email' => 'supermanager@company.com',
        //     'phone' => '+1-555-0201',
        //     'address' => '456 Oak Ave, City, State 12345',
        //     'hire_date' => '2022-06-01',
        //     'salary' => 85000.00,
        //     'department_id' => 2, // IT Department
        //     'role_id' => 2, // Team Lead role
        //     'status' => 'active'
        // ]);

        // // Create Manager User
        // User::create([
        //     'name' => 'David Chen',
        //     'email' => 'supermanager@company.com',
        //     'password' => Hash::make('password'),
        //     'employee_id' => $managerEmployee->id,
        //     'user_role' => 'manager',
        //     'is_active' => true,
        //     'email_verified_at' => now(),
        // ]);

        // Create Regular Employee
        $employee = Employee::create([
            'first_name' => 'Johney',
            'last_name' => 'Smith',
            'email' => 'normalemployee@company.com',
            'phone' => '+1-555-0301',
            'address' => '789 Pine St, City, State 12345',
            'hire_date' => '2023-01-15',
            'salary' => 65000.00,
            'department_id' => 2, // IT Department
            'role_id' => 3, // Software Developer role
            'manager_id' => 3,
            'status' => 'active'
        ]);

        // Create Employee User
        User::create([
            'name' => 'Johney Smith',
            'email' => 'normalemployee@company.com',
            'password' => Hash::make('password'),
            'employee_id' => $employee->id,
            'user_role' => 'employee',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
