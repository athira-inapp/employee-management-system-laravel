<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create HR Admin
        User::create([
            'name' => 'HR Administrator',
            'email' => 'hr@company.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_HR_ADMIN,
            'employee_id' => 'EMP001',
            'department_id' => 1, // HR Department
            'hire_date' => now()->subYears(2),
            'status' => 'active',
        ]);

        // Create Manager
        User::create([
            'name' => 'Department Manager',
            'email' => 'manager@company.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_MANAGER,
            'employee_id' => 'EMP002',
            'department_id' => 2, // IT Department
            'hire_date' => now()->subYear(),
            'status' => 'active',
        ]);

        // Create Employee
        User::create([
            'name' => 'John Employee',
            'email' => 'employee@company.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_EMPLOYEE,
            'employee_id' => 'EMP003',
            'department_id' => 2, // IT Department
            'hire_date' => now()->subMonths(6),
            'status' => 'active',
        ]);
    }
}