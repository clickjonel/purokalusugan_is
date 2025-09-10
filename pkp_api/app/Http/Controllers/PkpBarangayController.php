<?php


namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Pkp_barangay;

class PkpBarangayController extends Controller
{
    public function getBarangays(): JsonResponse
    {
        $data = Pkp_barangay::all();
        return response()->json([
            'message' => 'Data retrieved successfully',
            'data' => $data
        ], 200);
    }
    public function getBarangay(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'barangay_id' => 'required|integer|exists:pkp_barangay,barangay_id',
        ]);

        $data = Pkp_barangay::with(['municipality', 'province', 'region'])
            ->findOrFail($validatedData['barangay_id']);

        return response()->json([
            'message' => 'data retrieved successfully',
            'data' => $data
        ], 200);
    }

    public function getBarangaysByRegionId(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'region_id' => 'required|integer|exists:pkp_region,region_id',
        ]);

        $data = Pkp_barangay::where('region_id', $validatedData['region_id'])->get();

        return response()->json([
            'message' => 'data retrieved successfully',
            'data' => $data
        ], 200);
    }
    public function getBarangaysByProvinceId(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'province_id' => 'required|integer|exists:pkp_province,province_id',
        ]);

        $data = Pkp_barangay::where('province_id', $validatedData['province_id'])->get();

        return response()->json([
            'message' => 'data retrieved successfully',
            'data' => $data
        ], 200);
    }
    public function getBarangaysByMunicipalityId(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'municipality_id' => 'required|integer|exists:pkp_municipality,municipality_id',
        ]);

        $data = Pkp_barangay::where('municipality_id', $validatedData['municipality_id'])->get();

        return response()->json([
            'message' => 'data retrieved successfully',
            'data' => $data
        ], 200);
    }
}
