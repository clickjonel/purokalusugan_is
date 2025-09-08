<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_municipality extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_municipality';
    protected $fillable = [
        'region_id',
        'province_id',
        'municipality_name',
        'uid'
    ];
}
