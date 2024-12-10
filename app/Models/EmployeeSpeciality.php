<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSpeciality extends Model
{
    //
    protected $table = 'employees_specialities';

    protected $fillable = [
        'employee_id',
        'speciality_id',
    ];
}
