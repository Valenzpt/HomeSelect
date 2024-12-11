<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IncidentController extends Controller
{
    /**
     * Display a listing of incidents.
     */
    public function index()
    {
        $incidents = Incident::with(['apartment', 'tasks'])->get();

        if($incidents->isEmpty()){
            return response()->json([
                'message' => 'No incidents found',
                'status'=> 404
            ], 404);
        }
        //Group incidents by apartment
        $groupedIncidents = $incidents->groupBy(function ($incident) {
            return $incident->apartment->id;
        });

        //Map a new response incidents object by apartment and its tasks
        $result = $groupedIncidents->map(function ($incidents, $apartmentId) {
            $apartment = $incidents->first()->apartment; //Apartment details
            return [
                'apartment_name' => $apartment->name,
                'apartment_addres' => $apartment->address,
                'apartment_owner' => $apartment->owner,
                'incidents' => $incidents->map(function ($incident) {
                    return [
                        'incident_id' => $incident->id,
                        'description' => $incident->description,
                        'creation_date' => $incident->creation_date,
                        'tasks' => $incident->tasks->map(function ($task) {
                            return [
                                'description' => $task->description,
                                'status' => $task->status->description,
                                'responsible_cost' => $task->responsibleCost->type,
                                'additional_information' => $task->additional_information,
                                'cost' => $task->cost,
                                'created_at' => $task->created_at,
                            ];
                        }),
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
     * Store a newly created incident in storage.
     */
    public function store(Request $request)
    {
        //input validation
        $validator = Validator::make($request->all(), [
            'apartment_id' => 'required|exists:apartments,id',
            'description' => 'required|string'
        ]);
        if($validator->fails()) {
            return response()->json([
                'message' => 'Data validation errors',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
        //create incident
        $incident = Incident::create([
            'apartment_id' => $request->apartment_id,
            'description' => $request->description,
            'creation_date' => now(),
        ]);

        if(!$incident){
            return response()->json([
                'message' => 'Error creating incident',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'message' => 'Incident created successfully',
            'data' => $incident,
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified incident.
     */
    public function show(string $id)
    {
        $incident = Incident::with(['apartment', 'tasks'])->find($id);
        if(!$incident){
            return response()->json([
                'message' => 'Incident not found',
                'status' => 404
            ], 404);
        }

        $result = [
            'incident_id' => $incident->id,
            'description' => $incident->description,
            'creation_date' => $incident->creation_date,
            'apartment' => [
                'apartment_name' => $incident->apartment->name,
                'apartment_address' => $incident->apartment->address,
                'apartment_owner' => $incident->apartment->owner
            ],
            'tasks' => $incident->tasks->map(function ($task) {
                return [
                    'task_description' => $task->description,
                    'status' => $task->status->description ,
                    'responsible' => $task->responsibleCost->type,
                    'cost' => $task->cost
                ];
            }),
        ];
        
        return response()->json([
            'data' => $result,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified incident in storage.
     */
    public function update(Request $request, $id)
    {
        $incident = Incident::find($id);
        if(!$incident){
            return response()->json([
                'message' => 'Incident not found',
                'status' => 404
            ], 404);
        }
        //input validation
        $validator = Validator::make($request->all(), [
            'apartment_id' => 'required|exists:apartments,id',
            'description' => 'required|string'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Data validation errors',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $incident->update($request->only('description'));

        return response()->json([
            'message' => 'Incident updated successfully',
            'data' => $incident
        ], 201);
    }
}
