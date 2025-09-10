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
}
