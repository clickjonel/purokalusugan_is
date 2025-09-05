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
    public function getProgram(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'program_id' => 'required|integer|exists:pkp_program,program_id',            
        ]);
       
        $program = Programs::findOrFail($validatedData['program_id']);

        // $program = Programs::find($request->program_id);
        // if (!$program) {
        //     return response()->json([
        //         'message' => 'Program not found'
        //     ], 404);
        // }
        
        return response()->json([
            'message' => 'Program retrieved successfully',
            'data' => $program
        ], 200);
    }
    // update
    public function updateProgram(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'program_id' => 'required|integer|exists:pkp_program,program_id',
            'program_code' => 'required|string',
            'program_name' => 'required|string',            
            'program_status' => 'nullable|boolean'            
        ]);
       
        $program = Programs::find($validatedData['program_id'])
            ->update($validatedData);
        // if (!$program) {
        //     return response()->json([
        //         'message' => 'Program not found'
        //     ], 404);
        // }
        
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
