<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectManager extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectManagerFactory> */
    use HasFactory;
    protected $table = 'project_manager';
    protected $fillable = [
        'project_id',
        'manager_id',
        'status',
    ];

    public function project()
    {
        $this->belongsTo(Project::class);
    }
}
