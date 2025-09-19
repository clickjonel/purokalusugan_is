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
        'event_date_start',
        'event_date_end',
        'event_type',     
        'is_populated'  
    ];
    protected $primaryKey = 'event_id';
    protected $appends = [
        'event_type_name',
        'event_proponent_array',
        'event_partner_array'
    ];


    public function getEventTypeNameAttribute()
    {
        return $this->event_type === 1 ? 'Small Scale Event' : 'Large Scale Event';
    }

    public function getEventProponentArrayAttribute()
    {
        return explode(',',$this->event_proponents);
    }

    public function getEventPartnerArrayAttribute()
    {
        return explode(',',$this->event_partners);
    }

    public function programs()
    {
        return $this->belongsToMany(Programs::class, 'pkp_event_programs', 'event_id', 'program_id');
    }

    public function barangays()
    {
        return $this->belongsToMany(Pkp_barangay::class, 'pkp_event_barangays', 'event_id', 'barangay_id');
    }

    public function values()
    {
        return $this->hasMany(Pkp_indicator_values::class,'event_id','event_id');
    }

}
