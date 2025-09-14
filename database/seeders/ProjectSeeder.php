<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $project = [
            'project_name' => 'EASE',
            'start_date' => '2000-11-27',
            'end_date' => '2025-12-31',
            'status' => 'Live',
            'created_by' => 12
        ];

        Project::create($project);
    }
}
