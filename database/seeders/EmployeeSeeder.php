<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Department;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            // HR Department
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@company.com',
                'phone' => '+1-555-0101',
                'address' => '123 Main St, City, State 12345',
                'hire_date' => Carbon::now()->subYears(3)->format('Y-m-d'),
                'salary' => 75000.00,
                'department_id' => 1, // HR
                'role_id' => 1, // HR Manager
                'manager_id' => null, // Top level
                'status' => 'active'
            ]
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
