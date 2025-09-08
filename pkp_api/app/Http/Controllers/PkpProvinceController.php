<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Pkp_province;

class PkpProvinceController extends Controller
{
    public function getProvinces(): JsonResponse
    {
        $data = Pkp_province::all();
        return response()->json([
            'message' => 'Data retrieved successfully',
            'data' => $data
        ], 200);
    }
    public function getProvince(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'province_id' => 'required|integer|exists:pkp_province,province_id',
        ]);
        $data = Pkp_province::findOrFail($validatedData['province_id']);
        return response()->json([
            'message' => 'data retrieved successfully',
            'data' => $data
        ], 200);
    }
}
