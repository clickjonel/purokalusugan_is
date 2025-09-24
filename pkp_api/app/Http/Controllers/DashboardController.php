<?php

namespace App\Http\Controllers;

use App\Models\EventBarangay;
use App\Models\Hrh;
use App\Models\Pkp_events;
use App\Models\Pkp_indicator;
use App\Models\Pkp_indicator_values;
use App\Models\Pkp_site;
use App\Models\Programs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getExecDashboardData(): JsonResponse
    {
        $programs_count = Programs::count();
        $indicators_count = Pkp_indicator::count();
        $pk_sites_count = Pkp_site::count();
        $hrh_count = Hrh::where('user_level', 5)->count();
        // $indicators = Pkp_indicator::with(['program', 'disaggregations'])->get();
        $programs = Programs::withCount(['indicators'])->get();

        $eventData['total'] = Pkp_events::count();
        $eventData['total_budget_spent'] = Pkp_events::sum('event_actual_budget');
        $eventData['total_barangays'] = EventBarangay::count();
        $eventData['total_small_scale'] = Pkp_events::where('event_type',1)->count();
        $eventData['total_large_scale'] = Pkp_events::where('event_type',2)->count();

        return response()->json([
            'data' => [
                'programs_count' => $programs_count,
                'indicators_count' => $indicators_count,
                'pk_sites_count' => $pk_sites_count,
                'hrh_count' => $hrh_count,
                'programs' => $programs,
                'eventData' => $eventData
            ]
        ]);
    }

    public function getProgramDashboardData(Request $request)
    {
       $program = Programs::with(['indicators.disaggregations.indicatorDisaggregation.values'])->find($request->program_id);

        $program['indicators'] = $program['indicators']->map(function ($indicator) {
            // Get the indicator_disaggregation_ids for this indicator
            $indicator_disaggregation_ids = $indicator->disaggregations->pluck('pivot.indicator_disaggregation_id');

            // Fetch only the values for these indicator_disaggregation_ids
            $values = Pkp_indicator_values::whereIn('indicator_disaggregation_id', $indicator_disaggregation_ids)->get();

            // Assign the values to the indicator
            $indicator->values = $values;
            $indicator->sum_of_values = $values->sum('value');

            return $indicator;
        });

        return response()->json([
            'program' => $program,
        ]);
    }

}
