<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    protected $table = 'project';
    protected $fillable = [
        'project_name',
        'start_date',
        'end_date',
        'status',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function getProjectfullnameAttribute($value)
    {
        return ucfirst($this->project_name) . ' (' . $this->status . ')';
    }

    public function projectManager()
    {
        return $this->hasMany(ProjectManager::class);
    }
}
