<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamPelajaranMapelKelas extends Model
{
    protected $table = 'jam_pelajaran_mapel_kelas';
    protected $fillable = [
        'jampel_id',
        'mapel_kelas_id'
    ];
}
