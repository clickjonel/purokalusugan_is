<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Hrh extends Model
{
    protected $table = 'pkp_hrh_user';
    protected $primaryKey = 'pk_user_id';

    protected $fillable = [
        'user_code',
        // 'image',
        'username',
        'password',
        'user_level',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'nickname',
        'account_status',
    ];

    protected $appends = ['user_level_name'];

    public function getUserLevelNameAttribute()
    {
        $user_levels = [
            1 => 'Admin',
            2 => 'PK Committee Member',
            3 => 'Program Head',
            4 => 'C/PDOHO',
            5 => 'HRH'
        ];

        return $user_levels[$this->user_level] ?? 'Unknown';
    }
}
