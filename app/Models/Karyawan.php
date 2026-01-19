<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';

    protected $fillable = [
        'nama_karyawan',
        'email_karyawan',
        'departemen',
        'jabatan',
        'status',
    ];

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeNonaktif($query)
    {
        return $query->where('status', 'nonaktif');
    }

    // Relationships
    public function resepsionis()
    {
        return $this->hasOne(Resepsionis::class, 'id_karyawan', 'id_karyawan');
    }

    public function kunjungan()
    {
        return $this->belongsToMany(
            Kunjungan::class,
            'kunjungan_karyawan',
            'id_karyawan',
            'id_kunjungan'
        )->withPivot('id_kunjungan_karyawan');
    }
}
