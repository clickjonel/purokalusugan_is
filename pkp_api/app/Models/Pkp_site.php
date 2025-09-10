<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_site extends Model
{

    protected $table = 'pkp_site';
    protected $primaryKey = 'site_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'barangay_name',
        'barangay_id',
        'latitude',
        'longitude',
        'site_status',
        'no_purok',
        'no_sitio',
        'target_purok',
        'target_sition',
        'no_household',
        'population',
    ];
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'site_status' => 'integer',
        'no_purok' => 'integer',
        'no_sitio' => 'integer',
        'target_purok' => 'integer',
        'target_sition' => 'integer',
        'no_household' => 'integer',
        'population' => 'integer',
    ];
    public $timestamps = true;
    public function barangay()
    {
        return $this->belongsTo(Pkp_Barangay::class, 'barangay_id', 'barangay_id');
    }
}
