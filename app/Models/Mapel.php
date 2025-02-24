<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $fillable = [
        'nama',
        'parent',
        'kelas',
        'guru_id',
        'semester_id',
    ];

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'mapel_kelas', 'mapel_id', 'kelas_id');
    }

    public function guru()
    {
        return $this->belongsTo(Pendidik::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function mapelKelas()
    {
        return $this->hasMany(MapelKelas::class);
    }
}
