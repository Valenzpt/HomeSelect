<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks with relationships.
     */
    public function index()
    {
        $tasks = Task::with(['incident', 'status', 'responsibleCost'])->get();
        

        if($tasks->isEmpty()){
            return response()->json([
                'message' => 'No tasks found',
                'status'=> 404
            ], 404);
        }

        //Group tasks by incident
        $groupedTasks = $tasks->groupBy(function ($task) {
            return $task->incident->id;
        });
        //Map a new response object including incident data and its tasks
        $result = $groupedTasks->map(function ($tasks, $incidentId){
            return [
                'incident_id' => $incidentId,
                'incident_description' => $tasks->first()->incident->description,
                'tasks' => $tasks->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'status' => $task->status->description,
                        'responsible_cost' => $task->description,
                        'additional_information' => $task->additional_information,
                        'cost' => $task->cost,
                        'created_at' => $task->created_at,
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
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        //input validation
        $validator = Validator::make($request->all(), [
            'incident_id' => 'required|exists:incidents,id',
            'status_id' => 'required|exists:tasks_status,id',
            'responsible_cost_id' => 'required|exists:costs_responsibles,id',
            'description' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'additional_information' => 'nullable|string',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Data validation errors',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
        //Validation for solutions status
        if($request->has('status_id')){
            $status = $request->status_id;
            if(in_array($status, [3, 4]) && empty($request->additional_information)){
                var_dump($request->additional_information);
                return response()->json([
                    'message' => 'Please enter your comments on the solution'
                ], 400);
            }
        }
        //create task
        $task = Task::create($request->all());

        if(!$task){
            return response()->json([
                'message' => 'Error creating task',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task,
            'status' => 201
        ],201);
    }

    /**
     * Display the specified task.
     */
    public function show($id)
    {
        $task = Task::with(['incident', 'status', 'responsibleCost'])->find($id);
        if(!$task){
            return response()->json([
                'message' => 'Task not found',
                'status' => 404
            ], 404);
        }
        $result = [
            'tasks_id' => $task->id,
            'description' => $task->description,
            'status' => $task->status->description,
            'responsible_cost' => $task->responsibleCost->type,
            'additional_information' => $task->additional_information,
            'cost' => $task->cost,
            'created_at' => $task->created_at,
            'incident' => [
                'incident_id' => $task->incident->id,
                'incident_description' => $task->incident->description,
                'incident_creation_date' => $task->incident->creation_date,
            ]
        ];

        return response()->json([
            'data' => $result,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, $id)
    {
        $task = Task::find($id);
        if(!$task){
            return response()->json([
                'message' => 'Task not found',
                'status' => 404
            ], 404);
        }
        //input validation
        $validator = Validator::make($request->all(), [
            'incident_id' => 'required|exists:incidents,id',
            'status_id' => 'required|exists:tasks_status,id',
            'responsible_cost_id' => 'required|exists:costs_responsibles,id',
            'additional_information' => 'nullable',
            'cost' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Data validation errors',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
        //Validation for solutions status
        if($request->has('status_id')){
            $status = $request->status_id;
            if(in_array($status, [3, 4]) && empty($request->additional_information)){
                return response()->json([
                    'message' => 'Please enter your comments on the solution'
                ], 400);
            }
        }

        $task->update($request->all());

        return response()->json([
            'message' => 'Task updated successfully',
            'data' => $task
        ], 201);
    }
}
