<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $incidents = Incident::with(['apartment', 'tasks'])->get();

        if($incidents->isEmpty()){
            $data = [
                'message' => 'No se encontraron incidentes',
                'status'=> 200
            ];
            return response()->json($data, 200);
        }
        return response()->json($incidents, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'apartment_id' => 'required|exists:apartments,id',
            'description' => 'required'
        ]);
        if($validator->fails()) {
            $data = [
                'message' => 'Error en la validacion de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $incident = Incident::create([
            'apartment_id' => $request->apartment_id,
            'description' => $request->description,
            'creation_date' => now(),
        ]);

        if(!$incident){
            $data = [
                'message' => 'Error al crear incidencia',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'apartment' => $incident,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $incident = Incident::with(['apartment', 'tasks'])->find($id);
        if(!$incident){
            return response()->json(['error' => 'Incidencia no encontrada'], 404);
        }
        
        return response()->json($incident, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $incident = Incident::find($id);
        if(!$incident){
            return response()->json(['error' => 'Incidencia no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'apartment_id' => 'required|exists:apartments,id',
            'description' => 'required'
        ]);

        if($validator->fails()) {
            $data = [
                'message' => 'Error en la validacion de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $incident->update($request->only('description'));

        return response()->json([
            'message' => 'Incidencia actualizada exitosamente',
            'data' => $incident
        ], 200);
    }
}
