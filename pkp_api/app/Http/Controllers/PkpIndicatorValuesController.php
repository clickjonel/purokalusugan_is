<?php

namespace App\Http\Controllers;

use App\Models\Pkp_indicator_values;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PkpIndicatorValuesController extends Controller
{
    public function createIndicatorValue(Request $request):JsonResponse
    {
        $validatedData=$request->validate([            
            'event_id' => 'integer',
            'indicator_disaggregation_id' => 'integer',
            'value'=>'integer',
            'remarks' => 'string'     
        ]);
        $data=Pkp_indicator_values::create($validatedData);
         return response()->json([
            'message' => 'Created successfully',
            'data'=>$data
        ], 201);
    }
 
    public function getIndicatorValues(): JsonResponse
    {
        $data = Pkp_indicator_values::all();
        return response()->json([
            'message' => 'data retrieved successfully',
            'data' => $data
        ], 200);
    }
    public function updateIndicatorValue(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'indicator_value_id' => 'required|integer|exists:pkp_indicator_values,indicator_value_id',
            'event_id' => 'integer',
            'indicator_disaggregation_id' => 'integer',
            'value'=>'integer',
            'remarks' => 'string'           
        ]);
        $data = Pkp_indicator_values::find($validatedData['indicator_value_id'])
            ->update($validatedData);
        return response()->json([
            'message' => 'Updated successfully',
            'data' => $data
        ], 200);
    } 
    public function deleteIndicatorValue(Request $request): JsonResponse
    {
        $validatedData = $request->validate(rules: [
            'indicator_value_id' => 'required|integer|exists:pkp_indicator_values,indicator_value_id',            
        ]);
        Pkp_indicator_values::findOrFail($validatedData['indicator_value_id'])->delete();        
        return response()->json([
            'message' => 'Deleted successfully'
        ], 200);
    }
}
