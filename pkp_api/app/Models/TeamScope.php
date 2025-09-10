<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamScope extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_team_scope';
    protected $primaryKey = 'team_scope_id';

    protected $fillable = [
        'purok_id',
        'team_id'
    ];
}
