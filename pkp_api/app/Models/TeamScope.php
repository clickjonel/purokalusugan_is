<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamScope extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_team_scope';
    protected $primaryKey = 'team_scope_id';

    protected $fillable = [
        'barangay_id',
        'team_id'
    ];

    public function barangay()
    {
        return $this->belongsTo(Pkp_barangay::class,'barangay_id','barangay_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class,'team_id','team_id');
    }

}
