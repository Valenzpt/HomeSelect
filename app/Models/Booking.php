<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $fillable = [
        'customer_id',
        'apartment_id',
        'start_date',
        'end_date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }

    public static function isAvailable($apartment_id, $start_date, $end_date){
        return !self::where('apartment_id', $apartment_id)
            ->where(function ($query) use ($start_date, $end_date){
                $query->whereBetween('start_date', [$start_date, $end_date])
                    ->orWhereBetween('end_date', [$start_date, $end_date]);
        })->exists();
    }
}
