<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_indicator_disaggregation extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_indicator_disaggregations';
    protected $fillable = [
        'indicator_id',
        'disaggregation_id'
    ];
    protected $primaryKey = 'indicator_disaggregation_id';
}
