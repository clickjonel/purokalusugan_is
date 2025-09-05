<?php

namespace App\Http\Controllers;
use App\Models\Pkp_indicator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PkpIndicatorController extends Controller
{
    // create
    public function createIndicator(Request $request):JsonResponse
    {
        $validatedData=$request->validate([            
            'program_id' => 'required|integer',
            'indicator_code' => 'string',
            'indicator_name' => 'string',
            'indicator_description' => 'string',
            'indicator_status' => 'integer',
            'indicator_scope' => 'integer'            
        ]);
        $pkpIndicator=Pkp_indicator::create($validatedData);
         return response()->json([
            'message' => 'Created successfully',
            'data'=>$pkpIndicator
        ], 201);
    }

    // read all
    public function getIndicators(): JsonResponse
    {
        $pkpIndicators = Pkp_indicator::all();
        return response()->json([
            'message' => 'Indicators retrieved successfully',
            'data' => $pkpIndicators
        ], 200);
    }
    //read one
    public function getIndicator(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'indicator_id' => 'required|integer|exists:pkp_indicators,indicator_id',            
        ]);
        $pkpIndicator = Pkp_indicator::findOrFail($validatedData['indicator_id']);
        return response()->json([
            'message' => 'Indicator retrieved successfully',
            'data' => $pkpIndicator
        ], 200);
    }
    // update
    public function updateIndicator(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'indicator_id' => 'required|integer|exists:pkp_indicators,indicator_id',
            'program_id' => 'required|integer',
            'indicator_code' => 'sometimes|string',
            'indicator_name' => 'sometimes|string',
            'indicator_description' => 'sometimes|string',
            'indicator_status' => 'sometimes|integer',
            'indicator_scope' => 'sometimes|integer'
        ]);
        $pkpIndicator = Pkp_indicator::find($validatedData['indicator_id'])
            ->update($validatedData);        
        return response()->json([
            'message' => 'Updated successfully',
            'data' => $pkpIndicator
        ], 200);
    }    

    //delete    
    public function deleteIndicator(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'indicator_id' => 'required|integer|exists:pkp_indicators,indicator_id',            
        ]);
        Pkp_indicator::find($validatedData['indicator_id'])->delete();
        return response()->json([
            'message' => 'Deleted successfully'
        ], 200);
    }
}


