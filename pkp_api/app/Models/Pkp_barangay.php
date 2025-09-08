<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_barangay extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_barangay';
    protected $fillable = [
        'municipality_id',
        'region_id',
        'province_id',
        'barangay_name',
        'uid'
    ];
}