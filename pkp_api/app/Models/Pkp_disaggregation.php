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
        'totalable'
    ];
    public $timestamps = false;

    protected $primaryKey = 'disaggregation_id';

    public function indicator()
    {
        return $this->belongsToMany(
            Pkp_indicator::class,
            'pkp_indicator_disaggregations',
            'disaggregation_id',  // Foreign key in the pivot table for Pkp_disaggregation
            'indicator_id'        // Foreign key in the pivot table for Pkp_indicator
        );
    }
}
