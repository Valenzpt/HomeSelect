<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Apartment;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $apartments = Apartment::all();

        if($apartments->isEmpty()){
            $data = [
                'message' => 'No se encontraron apartamentos',
                'status'=> 200
            ];
            return response()->json($data, 200);
        }
        return response()->json($apartments, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'owner_id' => 'required|exists:owners,id',
            'name' => 'required',
            'address' => 'required',
        ]);
        if($validator->fails()) {
            $data = [
                'message' => 'Error en la validacion de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $apartment = Apartment::create([
            'name' => $request->name,
            'address' => $request->address,
            'owner_id' => $request->owner_id
        ]);

        if(!$apartment){
            $data = [
                'message' => 'Error al crear apartamento',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'apartment' => $apartment,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $apartment = Apartment::with(['owner'])->find($id);
        if(!$apartment){
            return response()->json(['error' => 'Apartamento no encontrado'], 404);
        }
        
        return response()->json($apartment, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $apartment = Apartment::find($id);
        if(!$apartment){
            return response()->json(['error' => 'Apartamento no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'owner_id' => 'required|exists:owners,id',
            'name' => 'required',
            'address' => 'required',
        ]);

        if($validator->fails()) {
            $data = [
                'message' => 'Error en la validacion de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $apartment->update([
            'name' => $request->name,
            'address' => $request->address,
            'owner_id' => $request->owner_id
        ]);

        return response()->json([
            'message' => 'Apartamento actualizado exitosamente',
            'data' => $apartment
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $apartment = Apartment::find($id);
        if(!$apartment){
            return response()->json(['error' => 'Apartamento no encontrado'], 404);
        }

        $apartment->delete();
        return response()->json([
            'message' => 'Apartamento eliminado exitosamente'
        ], 200);
    }
}
