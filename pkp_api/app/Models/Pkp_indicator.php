<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_indicator extends Model
{
    protected $connection = 'pkpulse';
    protected $fillable = [        
        'program_id',
        'indicator_code',
        'indicator_name',
        'indicator_description',
        'indicator_status',
        'indicator_scope'
    ];
    protected $primaryKey = 'indicator_id';
}
