<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{

    protected $connection = 'pkpulse';
    protected $table = 'pkp_team';
    protected $primaryKey = 'team_id';

    protected $fillable = [
        'team_name',
    ];

    public function scope():HasMany
    {
        return $this->hasMany(TeamScope::class, 'team_id', 'team_id');
    }

    public function members():HasMany
    {
        return $this->hasMany(TeamMember::class, 'team_id', 'team_id');
    }

}
