<?php

namespace App\Http\Controllers;

use App\Models\Programs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramsController extends Controller
{
    public function create():JsonResponse
    {
        //create
        // Programs::create([
        //     'name' => 'Nutrition Program',
        // ]);

        //update
        // Programs::find(1)->update([
        //     'name' => 'Updated Nutrition Program',
        // ]);

        //delete
        // Programs::find(1)->delete();


        return response()->json(['message' => 'Updated created successfully'], 200);

    }

    public function list()
    {
        return response()->json([
            'programs' => Programs::all()
        ]);
    }
    

}
