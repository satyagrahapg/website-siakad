<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomentarCK extends Model
{
    protected $fillable = [
        'komentar_tengah_semester',
        'komentar_akhir_semester',
        'mapel_id',
    ];

    /**
     * Define the relationship with the Mapel model.
     */
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }
}
