<?php

namespace App\Http\Controllers;

use App\Http\Requests\Indicator\CreateIndicatorRequest;
use App\IndicatorTrait;
use App\Models\Pkp_indicator;
use App\Models\Pkp_indicator_disaggregation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class PkpIndicatorController extends Controller
{
    use IndicatorTrait;

    // create
    public function createIndicator(CreateIndicatorRequest $request):JsonResponse
    {
        $validated = $request->validated();
        $indicator_code = $this->generateIndicatorCode($validated['program_id']);

        $pkpIndicator=Pkp_indicator::create([
            'program_id' => $validated['program_id'],
            'indicator_code' => $indicator_code,
            'indicator_name' => $validated['indicator_name'],
            'indicator_description' => $validated['indicator_description'],
            'indicator_status' => 1,
            'indicator_scope' => $validated['indicator_scope'],
        ]);
        
        $pkpIndicator->disaggregations()->attach([1]);

        return response()->json([
            'message' => 'Created successfully',
        ], 201);
    }

    // read all
    public function getIndicators(): JsonResponse
    {
        $pkpIndicators = Pkp_indicator::with(['program','disaggregations'])->get();
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
        $pkpIndicator = Pkp_indicator::with(['program','disaggregations'])->findOrFail($validatedData['indicator_id']);
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
    public function updateStatusOfProgram(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'indicator_id' => 'required|integer|exists:pkp_indicators,indicator_id',
            'indicator_status' => 'nullable|boolean'            
        ]);
        $indicator_id = Pkp_indicator::find($validatedData['indicator_id'])
            ->update($validatedData);
        return response()->json([
            'message' => 'Updated successfully',
            'data' => $indicator_id
        ], 200);
    } 

    public function removeDisaggregationOnIndicator(Request $request): JsonResponse
    {
        Pkp_indicator_disaggregation::find($request->indicator_disaggregation_id)->delete();

        return response()->json([
            'message' => 'Disaggregation Removed from Indicator'
        ]);
    }

    public function addIndicatorDisaggregations(Request $request): JsonResponse
    {
        $indicator = Pkp_indicator::find($request->indicator_id);
        $indicator->disaggregations()->attach($request->disaggregation_ids);

        return response()->json([
            'message' => 'Added Disaggregations to Indicator'
        ]);
    }
    
    
}


