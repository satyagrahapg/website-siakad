<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendidik extends Model
{
    protected $fillable = [
        'nama',
        'nip',
        'gelar',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'alamat',
        'jabatan',
        'status',
        'pangkat_golongan',
        'pendidikan',
        'id_user',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function getGelarDepanAttribute()
    {
        // Memotong string hingga karakter "|" (jika ada)
        $gelar = explode('|', $this->gelar);
        return $gelar[0] ?? null;  // Mengembalikan gelar depan atau null jika tidak ada
    }

    // Accessor untuk gelar belakang
    public function getGelarBelakangAttribute()
    {
        // Memotong string setelah karakter "|"
        $gelar = explode('|', $this->gelar);
        return $gelar[1] ?? null;  // Mengembalikan gelar belakang atau null jika tidak ada
    }

    // public function kelas()
    // {
    //     return $this->belongsTo(Kelas::class, 'id_guru');
    // }    
}
