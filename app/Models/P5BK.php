<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P5BK extends Model
{
    use HasFactory;

    protected $table = 'p5_b_k_s'; // Specify the table name if it's not the plural form of the model

    protected $fillable = [
        'status',
        'dimensi',
        'capaian',
        'siswa_id',
        'semester_id',
    ];

    // Relationship with Siswa model
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    // Relationship with Semester model
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
