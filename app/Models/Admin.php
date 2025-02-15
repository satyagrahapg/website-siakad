<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = [
        'nama',
        'nip',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'alamat',
        'jabatan',
        'status',
        'pangkat_golongan',
        'pendidikan',
        'id_user',  // Assuming there's a relationship with a `User` model
    ];

    // Define any relationships here, for example:
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
