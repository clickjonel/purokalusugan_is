<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_events extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_events';
    protected $fillable = [        
        'event_type',
        'event_date',
        'event_venue',
        'event_budget',
        'event_actual_budget',
        'event_fund_source',
        'event_proponent',
        'event_partner',
        'event_scope',
        'is_pk_site'        
    ];
    protected $primaryKey = 'event_id';
}
