<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamPelajaran extends Model
{
    protected $table = 'jam_pelajaran';
    protected $fillable = [
        'hari',
        'nomor',
        'event',
        'jam_mulai',
        'jam_selesai',
        'jam_mulai_calendar',
        'jam_selesai_calendar',
    ];
}
