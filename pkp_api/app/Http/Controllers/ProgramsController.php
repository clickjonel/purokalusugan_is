<?php

namespace App\Http\Controllers;

use App\Models\Programs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramsController extends Controller
{
    public function createProgram(Request $request):JsonResponse
    {
        $validatedData=$request->validate([            
            'program_code' => 'required|integer',
            'program_name' => 'string',
            'program_status' => 'string'          
        ]);
        $program=Programs::create($validatedData);
         return response()->json([
            'message' => 'Created successfully',
            'data'=>$program
        ], 201);
    }

    // read all
    public function getPrograms(): JsonResponse
    {
        $programs = Programs::all();
        return response()->json([
            'message' => 'Programs retrieved successfully',
            'data' => $programs
        ], 200);
    }
    //read one
    public function getProgram($id): JsonResponse
    {
        $program = Programs::find($id);
        if (!$program) {
            return response()->json([
                'message' => 'Program not found'
            ], 404);
        }
        return response()->json([
            'message' => 'Program retrieved successfully',
            'data' => $program
        ], 200);
    }
    // update
    public function updateProgram(Request $request, $id): JsonResponse
    {
        $program = Programs::find($id);
        if (!$program) {
            return response()->json([
                'message' => 'Program not found'
            ], 404);
        }
        $validatedData = $request->validate([
            'program_id' => 'sometimes|required|integer',
            'Program_code' => 'sometimes|string',
            'Program_name' => 'sometimes|string',
            'Program_description' => 'sometimes|string',
            'Program_status' => 'sometimes|integer',
            'Program_scope' => 'sometimes|integer'
        ]);
        $program->update($validatedData);
        return response()->json([
            'message' => 'Updated successfully',
            'data' => $program
        ], 200);
    }    

    //delete    
    public function deleteProgram($id): JsonResponse
    {
        $program = Programs::find($id);
        if (!$program) {
            return response()->json([
                'message' => 'Program not found'
            ], 404);
        }
        $program->delete();
        return response()->json([
            'message' => 'Deleted successfully'
        ], 200);
    }

}
