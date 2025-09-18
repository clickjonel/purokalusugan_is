<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_disaggregation extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_disaggregation';
    protected $fillable = [
        'disaggregation_code',        
        'disaggregation_name',
        'is_totalable'
    ];
    public $timestamps = false;

    protected $primaryKey = 'disaggregation_id';
}
