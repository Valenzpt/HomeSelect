<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $fillable = [
        'name',
        'phone_number',
        'email',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

}
