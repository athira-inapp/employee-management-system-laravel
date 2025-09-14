<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProjectManager;

class ProjectManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = [
            'project_id' => 1,
            'manager_id' => 12,
            'status' => 'Active'
        ];
        ProjectManager::create($manager);
    }
}
