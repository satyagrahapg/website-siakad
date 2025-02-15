<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianSiswa extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'penilaian_siswa';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'nilai',
        'remedial',
        'nilai_akhir',
        'penilaian_id',
        'siswa_id',
    ];

    /**
     * Define the relationship with the Penilaian model.
     */
    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class, 'penilaian_id');
    }

    /**
     * Define the relationship with the Siswa model.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
