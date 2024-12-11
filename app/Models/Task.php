<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    protected $table = 'tasks';

    protected $fillable = [
        'incident_id',
        'employee_id',
        'status_id',
        'responsible_cost_id',
        'description',
        'additional_information',
        'cost',
    ];

    
    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }

    public function employee()
    {
        return $this->belongsTo(MaintenanceEmployee::class, 'employee_id');
    }

    public function status()
    {
        return $this->belongsTo(TaskStatus::class, 'status_id');
    }

    public function responsibleCost()
    {
        return $this->belongsTo(CostsResponsible::class, 'responsible_cost_id');
    }
}
