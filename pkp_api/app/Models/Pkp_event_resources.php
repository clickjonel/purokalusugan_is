<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_event_resources extends Model
{
    protected $connection = 'pkpulse';
    protected $table = 'pkp_event_resources';
    protected  $primaryKey = 'event_resource_id';
    protected $fillable = [
        'name',
        'type',
        'beneficiary_count',
        'amount'
    ];
}
