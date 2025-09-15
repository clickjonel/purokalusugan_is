<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventProgram extends Model
{
    protected $table = 'pkp_event_programs';
    protected $primaryKey = 'event_program_id';
    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'program_id',
    ];

    public function program()
    {
        return $this->belongsTo(Programs::class, 'program_id', 'program_id');
    }

    public function event()
    {
        return $this->belongsTo(Pkp_events::class, 'event_id', 'event_id');
    }

}
