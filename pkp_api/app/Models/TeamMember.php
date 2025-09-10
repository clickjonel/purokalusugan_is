<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_team_member';
    protected $primaryKey = 'team_member_id';

    protected $fillable = [
        'hrh_id',
        'team_id',
        'member_role'
    ];

    protected $appends = ['member_role_name'];

    public function getMemberRoleNameAttribute()
    {
        return $this->member_role === 1 ? 'Team Leader' : 'Team Member';
    }

    public function team()
    {
        return $this->belongsTo(Team::class,'team_id','team_id');
    }

    public function hrh()
    {
        return $this->belongsTo(Hrh::class,'hrh_id','pk_user_id');
    }

}
