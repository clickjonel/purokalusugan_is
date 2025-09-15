<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventBarangay extends Model
{
    protected $table = 'pkp_event_barangays';
    protected $primaryKey = 'event_barangay_id';
    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'barangay_id',
    ];

    public function barangay()
    {
        return $this->belongsTo(Pkp_barangay::class,'barangay_id','barangay_id');
    }

    public function event()
    {
        return $this->belongsTo(Pkp_events::class,'event_id','event_id');
    }
}
