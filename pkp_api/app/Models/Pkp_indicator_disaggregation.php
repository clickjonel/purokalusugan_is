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

    public $timestamps = false;
    
    protected $primaryKey = 'indicator_disaggregation_id';
    public function indicator()
    {
        return $this->belongsTo(Pkp_indicator::class, 'indicator_id', 'indicator_id');
    }
    public function disaggregation()
    {
        return $this->belongsTo(Pkp_disaggregation::class, 'disaggregation_id', 'disaggregation_id');
    }

    public function values()
    {
        return $this->hasMany(Pkp_indicator_values::class, 'indicator_disaggregation_id', 'indicator_disaggregation_id');
    }
}
