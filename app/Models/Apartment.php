<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $table = 'apartments';

    protected $fillable = [
        'owner',
        'address',
        'name',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'apartment_id');
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class, 'apartment_id');
    }
}
