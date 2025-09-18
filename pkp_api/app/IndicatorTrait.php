<?php

namespace App;

use App\Models\Pkp_indicator;
use App\Models\Programs;
use Carbon\Carbon;

trait IndicatorTrait
{
    public function generateIndicatorCode($program_id):?string
    {
        $program = Programs::find($program_id);

        if (!$program) {
            return null;
        }

        $year = Carbon::now()->format('y');
        $latest_indicator = Pkp_indicator::latest('indicator_id')->first();
        
        $series = str_pad($latest_indicator ? $latest_indicator->indicator_id + 1 : 1, 4, '0', STR_PAD_LEFT);

        return "{$program->program_code}-IND-{$year}-{$series}";
    }
}
