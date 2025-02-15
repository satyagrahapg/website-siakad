<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasSiswa extends Model
{
    use HasFactory;

    protected $table = 'kelas_siswa';  // Make sure the table name matches your database table

    protected $fillable = [
        'kelas_id', // Foreign key for Kelas
        'siswa_id', // Foreign key for Siswa
    ];

    // Define relationship with Siswa model
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    // Define relationship with Kelas model
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
