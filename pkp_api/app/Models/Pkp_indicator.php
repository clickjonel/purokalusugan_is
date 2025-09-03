<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_indicator extends Model
{
    protected $connection = 'pkpulse';
    protected $fillable = [
        'indicator_id',
    ];
}
