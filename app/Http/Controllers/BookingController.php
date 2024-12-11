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
        $bookings = Booking::with(['customer', 'apartment'])->get();
        if($bookings->isEmpty()){
            $data = [
                'message' => 'No se encontraron reservas',
                'status'=> 200
            ];
            return response()->json($data, 200);
        };
        return response()->json($bookings, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'apartment_id' => 'required|exists:apartments,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);
        if($validator->fails()) {
            $data = [
                'message' => 'Error en la validacion de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }
       // return response()->json($validator, 200);
        if(!Booking::isAvailable($request->apartment_id, $request->start_date, $request->end_date)){
            return response()->json([
                'error' => 'El apartamento no esta disponible en las fechas seleccionadas',
                'status' => 422
            ]);
        }

        $booking = Booking::create([
            'customer_id' => $request->customer_id,
            'apartment_id' => $request->apartment_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        
        if(!$booking){
            $data = [
                'message' => 'Error al crear reserva',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'booking' => $booking,
            'status' => 200
        ];

        return response()->json([
            'message' => 'Reserva creada exitosamente',
            'data' => $data
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $booking = Booking::with(['customer', 'apartment'])->find($id);
        if(!$booking){
            return response()->json(['error' => 'Reserva no encontrada'], 404);
        }
        
        return response()->json($booking, 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);

        if(!$booking) {
            return response()->json([
                'error' => 'Reserva no encontrada',
                'status' => 404
            ]);
        }
        
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'apartment_id' => 'required|exists:apartments,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        if($validator->fails()) {
            $data = [
                'message' => 'Error en la validacion de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }
        
        if(!Booking::isAvailable($request->apartment_id, $request->start_date, $request->end_date, $booking->id)){
            return response()->json([
                'error' => 'El apartamento no esta disponible en las fechas seleccionadas',
                'status' => 422
            ]);
        }

        $booking->update([
            'customer_id' => $request->customer_id,
            'apartment_id' => $request->apartment_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'message' => 'Reserva actualizada exitosamente',
            'data' => $booking
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $booking = Booking::find($id);

        if(!$booking) {
            return response()->json([
                'error' => 'Reserva no encontrada',
                'status' => 404
            ]);
        }

        $booking->delete();
        return response()->json([
            'message' => 'Reserva eliminada exitosamente'
        ], 200);
    }
}
