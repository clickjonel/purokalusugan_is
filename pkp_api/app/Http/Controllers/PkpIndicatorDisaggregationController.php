<?php

namespace App\Http\Controllers;

use App\Models\Pkp_indicator_disaggregation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PkpIndicatorDisaggregationController extends Controller
{
    public function createIndicatorDisaggregation(Request $request):JsonResponse
    {
        $validatedData=$request->validate([            
            'indicator_id' => 'integer',
            'disaggregation_id' => 'integer'              
        ]);
        $data=Pkp_indicator_disaggregation::create($validatedData);
         return response()->json([
            'message' => 'Created successfully',
            'data'=>$data
        ], 201);
    }
 
    public function getIndicatorDiasaggregation(): JsonResponse
    {
        $data = Pkp_indicator_disaggregation::all();
        return response()->json([
            'message' => 'data retrieved successfully',
            'data' => $data
        ], 200);
    }
     public function updateIndicatorDisaggregation(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'indicator_value_id' => 'required|integer|exists:pkp_indicator_values,indicator_value_id',
            'event_id' => 'integer',
            'indicator_disaggregation_id' => 'integer',
            'value'=>'integer',
            'remarks' => 'string'           
        ]);
        $data = Pkp_indicator_disaggregation::find($validatedData['indicator_value_id'])
            ->update($validatedData);
        return response()->json([
            'message' => 'Updated successfully',
            'data' => $data
        ], 200);
    } 
    public function deleteIndicatorDisaggregation(Request $request): JsonResponse
    {
        $validatedData = $request->validate(rules: [
            'indicator_value_id' => 'required|integer|exists:pkp_indicator_values,indicator_value_id',            
        ]);
        Pkp_indicator_disaggregation::findOrFail($validatedData['indicator_value_id'])->delete();        
        return response()->json([
            'message' => 'Deleted successfully'
        ], 200);
    }
}
