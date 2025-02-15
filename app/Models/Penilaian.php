<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $fillable = [
        'tipe',
        'judul',
        'tanggal',
        'kktp',
        'keterangan',
        'mapel_kelas_id'
    ];

    /**
     * Get the related TP model.
     */
    public function tps()
    {
        return $this->belongsToMany(TP::class, 'penilaian_t_p_s', 'penilaian_id', 'tp_id');
    }

    public function penilaian_siswa()
    {
        return $this->hasMany(PenilaianSiswa::class);
    }

    public function penilaian_tp()
    {
        return $this->hasMany(PenilaianTP::class);
    }
}
