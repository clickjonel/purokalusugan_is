<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_region extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_region';
    protected $fillable = [        
        'region_name',
        'uid'        
    ];
}
