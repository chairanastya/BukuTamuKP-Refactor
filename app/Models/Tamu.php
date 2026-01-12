<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tamu extends Model
{
    protected $table = 'tamu';
    protected $primaryKey = 'id_tamu';
    protected $fillable = [
        'nama_tamu',
        'email_tamu',
        'instansi_tamu',
        'ktp_public_id',
    ];

    // Relationships

    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class, 'id_tamu', 'id_tamu');
    }
}
