<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $table = 'incidents';

    protected $fillable = [
        'apartment_id',
        'description',
        'creation_date',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'incident_id');
    }
}
