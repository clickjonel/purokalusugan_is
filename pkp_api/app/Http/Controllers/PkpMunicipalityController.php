<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Pkp_municipality;


class PkpMunicipalityController extends Controller
{
    public function getMunicipalities(): JsonResponse
    {
        $data = Pkp_municipality::all();
        return response()->json([
            'message' => 'Data retrieved successfully',
            'data' => $data
        ], 200);
    }
    public function getMunicipality(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'municipality_id' => 'required|integer|exists:pkp_municipality,municipality_id',
        ]);
        $data = Pkp_municipality::findOrFail($validatedData['municipality_id']);
        return response()->json([
            'message' => 'data retrieved successfully',
            'data' => $data
        ], 200);
    }
}