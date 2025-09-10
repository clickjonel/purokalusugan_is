<?php

namespace App\Http\Controllers;

use App\Models\Pkp_disaggregation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PkpDisaggregationController extends Controller
{
   public function createDisaggregation(Request $request):JsonResponse
    {
        $validatedData=$request->validate([            
            'disaggregation_code' => 'required|string',
            'disaggregation_name' => 'required|string'            
        ]);
        $program=Pkp_disaggregation::create($validatedData);
         return response()->json([
            'message' => 'Created successfully',
            'data'=>$program
        ], 201);
    }
 
    public function getDisaggregations(): JsonResponse
    {
        $data = Pkp_disaggregation::all();
        return response()->json([
            'message' => 'data retrieved successfully',
            'data' => $data
        ], 200);
    }
     public function updateDisaggregation(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'disaggregation_id' => 'required|integer|exists:pkp_disaggregation,disaggregation_id',
            'disaggregation_code' => 'required|string',            
            'disaggregation_name' => 'required|string'            
        ]);
        $data = Pkp_disaggregation::find($validatedData['disaggregation_id'])
            ->update($validatedData);
        return response()->json([
            'message' => 'Updated successfully',
            'data' => $data
        ], 200);
    } 
    public function deleteDisaggregation(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'disaggregation_id' => 'required|integer|exists:pkp_disaggregation,disaggregation_id',            
        ]);
        Pkp_disaggregation::findOrFail($validatedData['disaggregation_id'])->delete();        
        return response()->json([
            'message' => 'Deleted successfully'
        ], 200);
    }
}
