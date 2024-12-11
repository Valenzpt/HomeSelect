<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with(['incident', 'status', 'employee', 'responsibleCost'])->get();
        //If tasks is empty return error message
        if($tasks->isEmpty()){
            $data = [
                'message' => 'No se encontraron tareas',
                'status'=> 200// revisar estados
            ];
            return response()->json($data, 200);
        }
        return response()->json($tasks, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'incident_id' => 'required|exists:incidents,id',
            'employee_id' => 'required|exists:maintenance_employees,id',
            'status_id' => 'required|exists:tasks_status,id',
            'responsible_cost_id' => 'required|exists:costs_responsibles,id',
            'description' => 'required',
            'cost' => 'required|numeric',
        ]);

        if($validator->fails()) {
            $data = [
                'message' => 'Error en la validacion de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $task = Task::create([
            'incident_id' => $request->incident_id,
            'employee_id' => $request->employee_id,
            'status_id' => $request->status_id,
            'responsible_cost_id' => $request->responsible_cost_id,
            'description' => $request->description,
            'cost' => $request->cost
        ]);

        if(!$task){
            $data = [
                'message' => 'Error al crear tarea',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'apartment' => $task,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $task = Task::with(['incident', 'status', 'employee', 'responsibleCost'])->find($id);
        if(!$task){
            return response()->json(['error' => 'Tarea no encontrada'], 404);
        }
        
        return response()->json($task, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::with()->find($id);
        if(!$task){
            return response()->json(['error' => 'Tarea no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'incident_id' => 'required|exists:incidents,id',
            'employee_id' => 'required|exists:maintenance_employees,id',
            'status_id' => 'required|exists:tasks_status,id',
            'responsible_cost_id' => 'required|exists:costs_responsibles,id',
            'aditional_information' => 'nullable'
        ]);

        if($validator->fails()) {
            $data = [
                'message' => 'Error en la validacion de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }
        //explicar
        if($request->has('status_id')){
            $status = $request->status_id;
            if(in_array($status, [3, 4]) && empty($request->aditional_information)){
                return response()->json([
                    'message' => 'Por favor ingresar comentario sobre la solucion'
                ], 400);
            }
        }

        $task->update($request->all());

        return response()->json([
            'message' => 'Tarea actualizada exitosamente',
            'data' => $task
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
