<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $fillable = [
        'apartment_id',
        'customer',
        'start_date',
        'end_date',
    ];
    
    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }

    public static function isAvailable($apartment_id, $start_date, $end_date, $excludeBookingId = null){
        return !self::where('apartment_id', $apartment_id)
            ->where(function ($query) use ($start_date, $end_date){
                $query->whereBetween('start_date', [$start_date, $end_date])
                    ->orWhereBetween('end_date', [$start_date, $end_date])
                    ->orWhere(function ($subquery) use ($start_date, $end_date) {
                        $subquery->where('start_date', '<=', $start_date)
                                ->where('end_date', '>=', $end_date);
                    });
        })
        ->when($excludeBookingId, function ($query) use ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        })->exists();
    }
}
