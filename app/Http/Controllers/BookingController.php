<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Booking;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with(['apartment'])->get();
        if($bookings->isEmpty()){
            return response()->json( [
                'message' => 'No booking found',
                'status'=> 200
            ], 200);
        };

        $groupedBookings = $bookings->groupBy(function ($booking) {
            return $booking->apartment->id;
        });

        $result = $groupedBookings->map(function ($bookings, $apartmentId) {
            $apartment = $bookings->first()->apartment;
            return [
                'apartment_name' => $apartment->name,
                'apartment_address' => $apartment->address,
                'apartment_owner' => $apartment->owner,
                'bookings' => $bookings->map(function ($booking) {
                    return [
                        'booking_id' => $booking->id,
                        'customer' => $booking->customer,
                        'star_date' => $booking->start_date,
                        'end_date' => $booking->end_date
                    ];
                }),
            ];
        })->values();
        return response()->json([
            'data' => $result,
            'status' => 200
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //input validation
        $validator = Validator::make($request->all(), [
            'apartment_id' => 'required|exists:apartments,id',
            'customer' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);
        if($validator->fails()) {
            return response()->json([
                'message' => 'Data validation errors',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
        //Booking availability
        if(!Booking::isAvailable($request->apartment_id, $request->start_date, $request->end_date)){
            return response()->json([
                'error' => 'Apartment no available on selected dates',
                'status' => 409
            ], 409);
        }
        //create booking
        $booking = Booking::create([
            'apartment_id' => $request->apartment_id,
            'customer' => $request->customer,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        
        if(!$booking){
            return response()->json([
                'message' => 'Error creating booking',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'message' => 'Booking created successfully',
            'data' => $booking,
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified booking.
     */
    public function show($id)
    {
        $booking = Booking::with(['apartment'])->find($id);
        if(!$booking){
            return response()->json([
                'error' => 'No booking found',
                'status' => 404
            ], 404);
        }
        $result = [
            'customer' => $booking->customer,
            'start_date' => $booking->start_date,
            'end_date' => $booking->end_date,
            'apartment' => [
                'apartment_name' => $booking->apartment->name,
                'apartment_address' => $booking->apartment->address,
                'apartment_owner' => $booking->apartment->owner
            ]
        ];
        
        return response()->json([
            'data' => $result,
            'status' => 200
        ], 200);
    }


    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);

        if(!$booking) {
            return response()->json([
                'message' => 'No booking found',
                'status' => 404
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'apartment_id' => 'required|exists:apartments,id',
            'customer' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Data validation errors',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
        //Booking availability
        if(!Booking::isAvailable($request->apartment_id, $request->start_date, $request->end_date, $booking->id)){
            return response()->json([
                'error' => 'Apartment no available on selected dates',
                'status' => 409
            ], 409);
        }

        $booking->update([
            'apartment_id' => $request->apartment_id,
            'customer' => $request->customer,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'message' => 'Booking updated successfully',
            'data' => $booking
        ], 200);
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy($id)
    {
        $booking = Booking::find($id);

        if(!$booking) {
            return response()->json([
                'error' => 'Booking not found',
                'status' => 404
            ]);
        }

        $booking->delete();
        return response()->json([
            'message' => 'Booking removed successfully',
            'status' => 200
        ], 200);
    }
}
