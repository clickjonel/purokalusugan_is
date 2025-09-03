<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Programs extends Model
{
    protected $connection = 'pkp';
    protected $fillable = [
        'name'
    ];

}
