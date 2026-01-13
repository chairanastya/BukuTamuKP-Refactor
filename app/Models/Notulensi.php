<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notulensi extends Model
{
    protected $table = 'notulensi';
    protected $primaryKey = 'id_notulensi';

    protected $fillable = [
        'id_kunjungan',
        'anggota_rapat',
        'isi_notulensi',
        'token_karyawan',
        'token_tamu',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    // Relationships
    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'id_kunjungan', 'id_kunjungan');
    }
}