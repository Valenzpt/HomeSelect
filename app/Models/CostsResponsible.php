<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostsResponsible extends Model
{
    //
    protected $table = 'costs_responsibles';

    protected $fillable = [
        'type',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'responsible_cost_id');
    }
}
