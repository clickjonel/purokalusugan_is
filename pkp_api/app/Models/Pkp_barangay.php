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
    public function municipality()
    {
        return $this->belongsTo(Pkp_Municipality::class, 'municipality_id', 'municipality_id');
    }
    public function region()
    {
        return $this->belongsTo(Pkp_region::class, 'region_id', 'region_id');
    }
    public function province()
    {
        return $this->belongsTo(Pkp_province::class, 'province_id', 'province_id');
    }
}
