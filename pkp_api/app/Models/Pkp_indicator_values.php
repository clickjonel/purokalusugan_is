<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_indicator_values extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_indicator_values';
    protected $fillable = [
        'event_id',
        'indicator_disaggregation_id',
        'value',
        'remarks',
        'barangay_id'
    ];
    protected $primaryKey = 'indicator_value_id';

    public function joinEvent(){
        return $this->hasOne(Pkp_events::class,"event_id","event_id");
    }

    public function indicatorDisaggregation()
    {
        return $this->belongsTo(Pkp_indicator_disaggregation::class, 'indicator_disaggregation_id', 'indicator_disaggregation_id');
    }

    public function barangay(){
        return $this->hasOne(Pkp_barangay::class,"barangay_id","barangay_id");
    }
   
}
