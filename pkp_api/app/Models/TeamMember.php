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
}
