<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserAssignment extends Model
{
    protected $connection = 'dohis';
    protected $table = 'dohis_hrh_user_assignment';
    protected $primaryKey = 'hrh_user_assignment_id';

    public function user():HasOne
    {
        return $this->hasOne(User::class,'hrh_user_id','hrh_user_id');
    }

    // public function division():HasOne
    // {
    //     return $this->hasOne(Division::class,'division_id','division_id');
    // }

    // public function section():HasOne
    // {
    //     return $this->hasOne(Section::class,'section_id','section_id');
    // }

}
