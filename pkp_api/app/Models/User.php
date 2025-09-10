<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens;

    protected $table = 'pkp_user';
    protected $primaryKey = 'pk_user_id';

    // public function assignment()
    // {
    //     return $this->hasOne(UserAssignment::class, 'hrh_user_id', 'hrh_user_id');
    // }

}
