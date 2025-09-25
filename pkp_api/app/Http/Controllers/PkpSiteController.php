<?php

namespace App\Http\Controllers;

use App\Models\Pkp_site;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Type\Decimal;

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
        ])->get()->map(function ($site) {
            $site->total_no_sitio_purok = ($site->no_sitio ?? 0) + ($site->no_purok ?? 0);
            $site->total_target_sitio_purok = ($site->target_sitio ?? 0) + ($site->target_purok ?? 0);
            return $site;
        });

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

        $site->total_no_sitio_purok = ($site->no_sitio ?? 0) + ($site->no_purok ?? 0);
        $site->total_target_sitio_purok = ($site->target_sitio ?? 0) + ($site->target_purok ?? 0);

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

    public function dashboardSites()
    { // 1) Load sites with required relations
        $sites = Pkp_site::with([
            'barangay:barangay_id,municipality_id,barangay_name',
            'barangay.municipality:municipality_id,province_id,municipality_name',
            'barangay.municipality.province:province_id,province_name'
        ])->get()->map(function ($site) {
            // 2) Compute per-site totals (safe numeric coalesce)
            $site->total_no_sitio_purok = (int) (($site->no_sitio ?? 0) + ($site->no_purok ?? 0));
            $site->total_target_sitio_purok = (int) (($site->target_sitio ?? 0) + ($site->target_purok ?? 0));

            // 3) Attach flattened identifiers / names to avoid nested access issues later
            $site->province_id = data_get($site, 'barangay.municipality.province.province_id');
            $site->province_name = data_get($site, 'barangay.municipality.province.province_name');
            $site->municipality_id = data_get($site, 'barangay.municipality.municipality_id');
            $site->municipality_name = data_get($site, 'barangay.municipality.municipality_name');
            $site->barangay_id = data_get($site, key: 'barangay.barangay_id');
            $site->barangay_name = data_get($site, 'barangay.barangay_name');

            return $site;
        });

        // Optional: filter out sites with no province if you don't want them included
        // $sites = $sites->filter(fn($s) => !is_null($s->province_id));

        // 4) Group by province_id (safe even if null)
        $provinces = $sites->groupBy('province_id')->map(function ($provinceSites, $provinceId) {
            $provinceName = optional($provinceSites->first())->province_name ?? 'Unknown';

            // 5) Group by municipality inside this province
            $municipalities = $provinceSites->groupBy('municipality_id')->map(function ($municipalitySites, $municipalityId) {
                $municipalityName = optional($municipalitySites->first())->municipality_name ?? 'Unknown';

                return [
                    'municipality_id' => $municipalityId,
                    'municipality_name' => $municipalityName,
                    'total_no_sitio_purok' => (int) $municipalitySites->sum('total_no_sitio_purok'),
                    'total_target_sitio_purok' => (int) $municipalitySites->sum('total_target_sitio_purok'),
                    // keep only necessary site fields to reduce payload bloat
                    'sites' => $municipalitySites->values()->map(function ($s) {
                        return [
                            'barangay_id' => $s->barangay_id,
                            'barangay_name' => $s->barangay_name,
                            'total_no_sitio_purok' => (int) $s->total_no_sitio_purok,
                            'total_target_sitio_purok' => (int) $s->total_target_sitio_purok,
                            'longitude' => ($s->longitude ?? 0),
                            'latitude' => ($s->latitude ?? 0),
                            // add other site fields you need here (status, coords, etc.)
                        ];
                    })->values(),
                ];
            })->values();

            return [
                'province_id' => $provinceId,
                'province_name' => $provinceName,
                'total_no_sitio_purok' => (int) $provinceSites->sum('total_no_sitio_purok'),
                'total_target_sitio_purok' => (int) $provinceSites->sum('total_target_sitio_purok'),
                'municipalities' => $municipalities,
            ];
        })->values();

        // 6) Optionally compute grand totals across all provinces
        $grandTotals = [
            'total_no_sitio_purok' => (int) $sites->sum('total_no_sitio_purok'),
            'total_target_sitio_purok' => (int) $sites->sum('total_target_sitio_purok'),
            'site_count' => $sites->count(),
        ];

        return response()->json([
            'message' => 'Sites retrieved successfully',
            'grand_totals' => $grandTotals,
            'data' => $provinces
        ], 200);
    }


    public function dashboardProvinceTotals()
    {
        $sites = DB::table('pkp_site')
            ->leftJoin('pkp_barangay', 'pkp_site.barangay_id', '=', 'pkp_barangay.barangay_id')
            ->leftJoin('pkp_municipality', 'pkp_barangay.municipality_id', '=', 'pkp_municipality.municipality_id')
            ->leftJoin('pkp_province', 'pkp_municipality.province_id', '=', 'pkp_province.province_id')
            ->select(
                'pkp_province.province_id',
                'pkp_province.province_name',
                'pkp_site.site_id',
                DB::raw('SUM(COALESCE(pkp_site.no_sitio,0) + COALESCE(pkp_site.no_purok,0)) as total_no_sitio_purok'),
                DB::raw('SUM(COALESCE(pkp_site.target_sitio,0) + COALESCE(pkp_site.target_purok,0)) as total_target_sitio_purok'),
                DB::raw('COUNT(pkp_site.site_id) as site_count')
            )
            ->groupBy('pkp_province.province_id', 'pkp_province.province_name')
            ->get();

        return response()->json([
            'message' => 'Provinces sites retrieved successfully',
            'data' => $sites
        ], 200);
    }

    public function dashboardMunicipalityTotals()
    {
        $municipalityTotals = DB::table('pkp_site')
            ->leftJoin('pkp_barangay', 'pkp_site.barangay_id', '=', 'pkp_barangay.barangay_id')
            ->leftJoin('pkp_municipality', 'pkp_barangay.municipality_id', '=', 'pkp_municipality.municipality_id')
            ->leftJoin('pkp_province', 'pkp_municipality.province_id', '=', 'pkp_province.province_id')
            ->select(
                'pkp_province.province_id',
                'pkp_province.province_name',
                'pkp_municipality.municipality_id',
                'pkp_municipality.municipality_name',
                DB::raw('SUM(COALESCE(pkp_site.no_sitio,0) + COALESCE(pkp_site.no_purok,0)) as total_no_sitio_purok'),
                DB::raw('SUM(COALESCE(pkp_site.target_sitio,0) + COALESCE(pkp_site.target_purok,0)) as total_target_sitio_purok'),
                DB::raw('COUNT(pkp_site.site_id) as site_count')
            )
            ->groupBy(
                'pkp_province.province_id',
                'pkp_province.province_name',
                'pkp_municipality.municipality_id',
                'pkp_municipality.municipality_name'
            )
            ->get();

        return response()->json([
            'message' => 'Municipality totals retrieved successfully',
            'data' => $municipalityTotals
        ], 200);
    }
    public function dashboardBarangayTotals()
    {
        $barangayTotals = DB::table('pkp_site')
            ->leftJoin('pkp_barangay', 'pkp_site.barangay_id', '=', 'pkp_barangay.barangay_id')
            ->leftJoin('pkp_municipality', 'pkp_barangay.municipality_id', '=', 'pkp_municipality.municipality_id')
            ->leftJoin('pkp_province', 'pkp_municipality.province_id', '=', 'pkp_province.province_id')
            ->select(
                'pkp_province.province_id',
                'pkp_province.province_name',
                'pkp_municipality.municipality_id',
                'pkp_municipality.municipality_name',
                'pkp_barangay.barangay_id',
                'pkp_barangay.barangay_name',
                'pkp_site.longitude',
                'pkp_site.latitude',
                DB::raw('SUM(COALESCE(pkp_site.no_sitio,0) + COALESCE(pkp_site.no_purok,0)) as total_no_sitio_purok'),
                DB::raw('SUM(COALESCE(pkp_site.target_sitio,0) + COALESCE(pkp_site.target_purok,0)) as total_target_sitio_purok'),
                DB::raw('COUNT(pkp_site.site_id) as site_count')
            )
            ->groupBy(
                'pkp_province.province_id',
                'pkp_province.province_name',
                'pkp_municipality.municipality_id',
                'pkp_municipality.municipality_name',
                'pkp_barangay.barangay_id',
                'pkp_barangay.barangay_name'
            )
            ->get();

        return response()->json([
            'message' => 'Barangay totals retrieved successfully',
            'data' => $barangayTotals
        ], 200);
    }
    public function dashboardRegionTotals()
    {
        // Get province totals per region (flat structure)
        $provinceTotals = DB::table('pkp_site')
            ->leftJoin('pkp_barangay', 'pkp_site.barangay_id', '=', 'pkp_barangay.barangay_id')
            ->leftJoin('pkp_municipality', 'pkp_barangay.municipality_id', '=', 'pkp_municipality.municipality_id')
            ->leftJoin('pkp_province', 'pkp_municipality.province_id', '=', 'pkp_province.province_id')
            ->leftJoin('pkp_region', 'pkp_province.region_id', '=', 'pkp_region.region_id')
            ->select(
                'pkp_region.region_id',
                'pkp_region.region_name',
                'pkp_province.province_id',
                'pkp_province.province_name',
                'pkp_site.site_id',
                DB::raw('SUM(COALESCE(pkp_site.no_sitio,0) + COALESCE(pkp_site.no_purok,0)) as total_no_sitio_purok'),
                DB::raw('SUM(COALESCE(pkp_site.target_sitio,0) + COALESCE(pkp_site.target_purok,0)) as total_target_sitio_purok'),
                DB::raw('COUNT(pkp_site.site_id) as site_count')
            )
            ->groupBy(
                'pkp_region.region_id',
                'pkp_region.region_name',
                'pkp_province.province_id',
                'pkp_province.province_name'
            )
            ->get();

        // Calculate grand totals
        $grandTotals = [
            'total_no_sitio_purok' => (int) $provinceTotals->sum('total_no_sitio_purok'),
            'total_target_sitio_purok' => (int) $provinceTotals->sum('total_target_sitio_purok'),
            'site_count' => (int) $provinceTotals->sum('site_count'),
        ];

        return response()->json([
            'message' => 'Regional totals retrieved successfully',
            'grand_totals' => $grandTotals,
            'data' => $provinceTotals,
        ], 200);
    }
}
