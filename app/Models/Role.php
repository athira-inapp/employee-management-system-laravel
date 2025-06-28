<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'base_salary'
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
    ];

    // Relationships
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
