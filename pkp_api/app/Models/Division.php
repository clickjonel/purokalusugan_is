<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $connection = 'dohis';
    protected $table = 'dohis_division';
    protected $primaryKey = 'division_id';

}
