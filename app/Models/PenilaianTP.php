<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianTP extends Model
{
    protected $table = 'penilaian_t_p_s';
    protected $fillable = [
        'penilaian_id',
        'tp_id',
    ];

    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class, 'penilaian_id');
    }

    public function tp()
    {
        return $this->belongsTo(TP::class, 'tp_id');
    }
}
