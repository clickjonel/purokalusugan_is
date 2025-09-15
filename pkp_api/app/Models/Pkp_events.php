<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_events extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_events';
    protected $fillable = [        
        'event_name',
        'event_venue',
        'event_budget',
        'event_actual_budget',
        'event_fund_source',
        'event_proponents',
        'event_partners',
        'event_date',
        'event_type',       
    ];
    protected $primaryKey = 'event_id';
    protected $appends = [
        'event_type_name'
    ];


    public function getEventTypeNameAttribute()
    {
        return $this->event_type === 1 ? 'Small Scale Event' : 'Large Scale Event';
    }
    public function programs()
    {
        return $this->belongsToMany(Programs::class, 'pkp_event_programs', 'event_id', 'program_id');
    }

    public function barangays()
    {
        return $this->belongsToMany(Pkp_barangay::class, 'pkp_event_barangays', 'event_id', 'barangay_id');
    }

}
