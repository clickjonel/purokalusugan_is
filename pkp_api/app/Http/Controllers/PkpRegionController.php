<?php

namespace App\Http\Controllers;
use App\Models\Pkp_region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PkpRegionController extends Controller
{
    public function getRegions(): JsonResponse
    {
        $data = Pkp_region::all();
        return response()->json([
            'message' => 'Data retrieved successfully',
            'data' => $data
        ], 200);
    }
     public function getRegion(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'region_id' => 'required|integer|exists:pkp_region,region_id',            
        ]);
        $data = Pkp_region::findOrFail($validatedData['region_id']);
        return response()->json([
            'message' => 'data retrieved successfully',
            'data' => $data
        ], 200);
    }
}
