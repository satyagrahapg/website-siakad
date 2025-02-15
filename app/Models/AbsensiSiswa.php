<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiSiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_siswa',
        'date',
        'status',
    ];

    // Optionally, define a relationship with the Siswa model
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
