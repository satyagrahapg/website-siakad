<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapelKelas extends Model
{
    protected $table = 'mapel_kelas';
    protected $fillable = [
        'kelas_id',
        'mapel_id',
    ];

    /**
     * Define the relationship with the Penilaian model.
     */
    public function mapel()
    {
        return $this->hasMany(Mapel::class, 'mapel_id');
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'kelas_id');
    }
    
    public function jampel()
    {
        return $this->belongsToMany(JamPelajaran::class, 'jam_pelajaran_mapel_kelas', 'mapel_kelas_id', 'jampel_id');
    }
}
