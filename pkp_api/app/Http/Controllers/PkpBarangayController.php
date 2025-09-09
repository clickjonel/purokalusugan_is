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
        $data = Pkp_barangay::findOrFail($validatedData['barangay_id']);
        return response()->json([
            'message' => 'data retrieved successfully',
            'data' => $data
        ], 200);
    }
}
