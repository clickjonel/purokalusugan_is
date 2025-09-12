<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Programs extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_program';
    protected $fillable = [        
        'program_code',
        'program_name',
        'program_status'
    ];
    protected $primaryKey = 'program_id';

    public function indicators()
    {
        return $this->hasMany(Pkp_indicator::class,'program_id','program_id');
    }
}
