<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    //
    protected $table = 'tasks_status';

    protected $fillable = [
        'description'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'status_id');
    }
}
