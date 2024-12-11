<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Apartment;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the apartments.
     */
    public function index()
    {
        $apartments = Apartment::all();

        if($apartments->isEmpty()){
            return response()->json([
                'message' => 'No apartments found',
                'status'=> 404
            ], 404);
        }
        return response()->json([
            'data' => $apartments,
            'status' => 200
        ], 200);
    }

    /**
     * Store a newly created apartment in storage.
     */
    public function store(Request $request)
    {
        //input validation
        $validator = Validator::make($request->all(), [
            'owner' => 'required',
            'name' => 'required',
            'address' => 'required',
        ]);
        if($validator->fails()) {
            return response()->json([
                'message' => 'Data validation errors',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        //create apartment
        $apartment = Apartment::create([
            'name' => $request->name,
            'address' => $request->address,
            'owner' => $request->owner
        ]);

        if(!$apartment){
            return response()->json([
                'message' => 'Error creating apartment',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'data' => $apartment,
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified apartment.
     */
    public function show($id)
    {
        $apartment = Apartment::find($id);
        if(!$apartment){
            return response()->json([
                'message' => 'Apartment not found',
                'status' => 404
            ], 404);
        }
        
        return response()->json([
            'data' => $apartment,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified apartment in storage.
     */
    public function update(Request $request, $id)
    {
        $apartment = Apartment::find($id);
        if(!$apartment){
            return response()->json([
                'message' => 'Apartment not found',
                'status' => 404
            ], 404);
        }
        //input validation
        $validator = Validator::make($request->all(), [
            'owner' => 'required',
            'name' => 'required',
            'address' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Data validation errors',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $apartment->update([
            'name' => $request->name,
            'address' => $request->address,
            'owner' => $request->owner
        ]);

        return response()->json([
            'message' => 'Apartment updated successfully',
            'data' => $apartment
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $apartment = Apartment::find($id);
        if(!$apartment){
            return response()->json([
                'message' => 'Apartment not found',
                'status' => 404
            ], 404);
        }

        $apartment->delete();
        return response()->json([
            'message' => 'Apartment deleted successfully'
        ], 200);
    }
}
