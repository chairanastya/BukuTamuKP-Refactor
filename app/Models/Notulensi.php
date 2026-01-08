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
    ];

    // Relationships
    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'id_kunjungan', 'id_kunjungan');
    }
}