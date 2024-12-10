<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceEmployee extends Model
{
    //
    protected $table = 'maintenance_employees';

    protected $fillable = [
        'name',
        'phone_number',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'employee_id');
    }

    public function specialities()
    {
        return $this->belongsToMany(Speciality::class, 'employee_specialities', 'employee_id', 'speciality_id');
    }

}
