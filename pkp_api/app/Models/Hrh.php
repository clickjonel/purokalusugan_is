<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hrh extends Model
{
    protected $table = 'pkp_hrh_user';
    protected $primaryKey = 'hrh_user_id';

    protected $fillable = [
        'user_code',
        'image',
        'username',
        'password',
        'prefix',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'nickname',
        'email_address',
        'contact_number',
        'account_status',
    ];
}
