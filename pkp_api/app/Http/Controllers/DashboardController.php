<?php

namespace App\Http\Controllers;

use App\Models\Hrh;
use App\Models\Pkp_indicator;
use App\Models\Pkp_site;
use App\Models\Programs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getExecDashboardData():JsonResponse
    {
        $programs_count = Programs::count();
        $indicators_count = Pkp_indicator::count();
        $pk_sites_count = Pkp_site::count();
        $hrh_count = Hrh::where('user_level',5)->count();
        $indicators = Pkp_indicator::with(['program','disaggregations'])->get();
        $programs = Programs::withCount(['indicators'])->get();

        return response()->json([
            'data' => [
                'programs_count' => $programs_count,
                'indicators_count' => $indicators_count,
                'pk_sites_count' => $pk_sites_count,
                'hrh_count' => $hrh_count,
                'indicators' => $indicators,
                'programs' => $programs
            ]
        ]);
    }
}
