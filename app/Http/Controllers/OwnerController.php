<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Owner;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = Owner::all();

        if($owners->isEmpty()){
            $data = [
                'message' => 'No se encontraron propietarios',
                'status'=> 200
            ];
            return response()->json($data, 200);
        }
        return response()->json($owners, 200);
    }
}
