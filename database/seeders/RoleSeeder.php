<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'HR Manager',
                'description' => 'Human Resources Manager',
                'base_salary' => 75000.00
            ],
            [
                'name' => 'Software Developer',
                'description' => 'Software Development Engineer',
                'base_salary' => 65000.00
            ],
            [
                'name' => 'Project Manager',
                'description' => 'Project Management Professional',
                'base_salary' => 70000.00
            ],
            [
                'name' => 'QA Engineer',
                'description' => 'Quality Assurance Engineer',
                'base_salary' => 55000.00
            ],
            [
                'name' => 'DevOps Engineer',
                'description' => 'DevOps and Infrastructure Engineer',
                'base_salary' => 70000.00
            ]
        ];
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
