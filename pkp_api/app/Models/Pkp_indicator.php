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

    protected $appends = [
        'indicator_scope_name',
        'indicator_status_name',
    ];

    public function getIndicatorScopeNameAttribute()
    {
        return $this->indicator_scope === 1 ? 'Individual' : 'Household';
    }

    public function getIndicatorStatusNameAttribute()
    {
        return $this->indicator_status === 1 ? 'Active' : 'Deactivated';
    }

    public function program()
    {
        return $this->belongsTo(Programs::class,'program_id','program_id');
    }

    public function disaggregations()
    {
       return $this->belongsToMany(Pkp_disaggregation::class, 'pkp_indicator_disaggregations', 'disaggregation_id', 'indicator_disaggregation_id');
    }
}
