<?php

namespace App\Http\Controllers;

use App\Models\Pkp_site;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PkpSiteController extends Controller
{
    //
    public function createSite(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'barangay_id' => 'required|integer|exists:pkp_barangay,barangay_id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'site_status' => 'nullable|integer',
            'no_purok' => 'nullable|integer',
            'no_sitio' => 'nullable|integer',
            'target_purok' => 'nullable|integer',
            'target_sitio' => 'nullable|integer',
            'no_household' => 'nullable|integer',
            'population' => 'nullable|integer',
        ]);

        $site = Pkp_site::create($validatedData);
        return response()->json([
            'message' => 'Site created successfully',
            'data' => $site
        ], 201);
    }
    public function getSites(): JsonResponse
    {
        $sites = Pkp_site::with([
            'barangay:barangay_id,municipality_id,barangay_name',
            'barangay.municipality:municipality_id,province_id,municipality_name',
            'barangay.municipality.province:province_id,province_name'
        ])->get();

        return response()->json([
            'message' => 'Sites retrieved successfully',
            'data' => $sites
        ], 200);
    }

    public function getSite(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'site_id' => 'required|integer|exists:pkp_site,site_id',
        ]);

        $site = Pkp_site::with([
            'barangay:barangay_id,municipality_id,barangay_name',
            'barangay.municipality:municipality_id,province_id,municipality_name',
            'barangay.municipality.province:province_id,province_name'
        ])->findOrFail($validatedData['site_id']);

        return response()->json([
            'message' => 'Site retrieved successfully',
            'data' => $site
        ], 200);
    }

    public function updateSite(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'site_id' => 'required|integer|exists:pkp_site,site_id',
            'barangay_id' => 'required|integer|exists:pkp_barangay,barangay_id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'site_status' => 'nullable|integer',
            'no_purok' => 'nullable|integer',
            'no_sitio' => 'nullable|integer',
            'target_purok' => 'nullable|integer',
            'target_sitio' => 'nullable|integer',
            'no_household' => 'nullable|integer',
            'population' => 'nullable|integer',
        ]);
        $site = Pkp_site::find($validatedData['site_id'])
            ->update($validatedData);
        return response()->json([
            'message' => 'Site updated successfully',
            'data' => $site
        ], 200);
    }
    public function deleteSite(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'site_id' => 'required|integer|exists:pkp_site,site_id',
        ]);
        Pkp_site::findOrFail($validatedData['site_id'])->delete();
        return response()->json([
            'message' => 'Site deleted successfully'
        ], 200);
    }
}
