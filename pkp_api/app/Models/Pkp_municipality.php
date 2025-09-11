<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_municipality extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_municipality';
    protected $primaryKey = 'municipality_id';
    protected $fillable = [
        'region_id',
        'province_id',
        'municipality_name',
        'uid'
    ];


    public function region()
    {
        return $this->belongsTo(Pkp_region::class, 'region_id', 'region_id')
            ->select('region_id', 'region_name');
    }

    public function province()
    {
        return $this->belongsTo(Pkp_province::class, 'province_id', 'province_id')
            ->select('province_id', 'province_name');
    }
}
