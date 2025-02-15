<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;
    protected $table = 'siswas';
    protected $fillable = [
        'nama',
        // 'nis',
        'nisn',
        'tanggal_lahir',
        'tempat_lahir',
        'jenis_kelamin',
        'agama',
        'status_keluarga',
        'anak_ke',
        'alamat',
        'telepon',
        'asal_sekolah',
        'tanggal_diterima',
        'jalur_penerimaan',
        'nama_ayah',
        'nama_ibu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'nama_wali',
        // 'alamat_wali',
        'pekerjaan_wali',
        'angkatan',
        'id_user',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user'); // Assuming 'id_user' is the foreign key
    }

    public function kelases()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_siswa');
    }

}
