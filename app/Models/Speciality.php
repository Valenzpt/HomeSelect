<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    //
    protected $table = 'specialities';
    protected $fillable = [
        'description',
    ];

    
    public function employees()
    {
        return $this->belongsToMany(MaintenanceEmployee::class, 'employees_specialities', 'speciality_id', 'employee_id');
    }
}
