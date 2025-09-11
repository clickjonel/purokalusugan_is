<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_province extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_province';
    protected $primaryKey = 'province_id';
    protected $fillable = [
        'region_id',
        'province_name',
        'uid'
    ];
    public function region()
    {
        return $this->belongsTo(Pkp_region::class, 'region_id', 'region_id')
            ->select('region_id', 'region_name');
    }
}
