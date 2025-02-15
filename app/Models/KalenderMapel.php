<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KalenderMapel extends Model
{
    protected $fillable = ['kelas_id', 'mapel_id', 'start_time', 'end_time'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }
}
