<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KunjunganKaryawan extends Model
{
    protected $table = 'kunjungan_karyawan';
    protected $primaryKey = 'id_kunjungan_karyawan';

    public $timestamps = false;

    protected $fillable = [
        'id_kunjungan',
        'id_karyawan',
    ];

    // Relationships
    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'id_kunjungan', 'id_kunjungan');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}
