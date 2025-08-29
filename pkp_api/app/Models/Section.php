<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $connection = 'dohis';
    protected $table = 'dohis_section';
    protected $primaryKey = 'section_id';
}
