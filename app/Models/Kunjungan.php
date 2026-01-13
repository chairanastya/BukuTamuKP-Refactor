<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $tanggal_kunjungan
 * @property \Carbon\Carbon|null $expired_at
 */
class Kunjungan extends Model
{
    protected $table = 'kunjungan';
    protected $primaryKey = 'id_kunjungan';

    protected $fillable = [
        'id_tamu',
        'tujuan_kunjungan',
        'tanggal_kunjungan',
        'jam_mulai',
        'jam_selesai',
        'status',
        'alasan_batal',
        'token_approval',
        'expired_at',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'expired_at' => 'datetime',
    ];

    // Relationships
    public function tamu()
    {
        return $this->belongsTo(Tamu::class, 'id_tamu', 'id_tamu');
    }

    public function karyawan()
    {
        return $this->belongsToMany(
            Karyawan::class,
            'kunjungan_karyawan',
            'id_kunjungan',
            'id_karyawan'
        )->withPivot('id_kunjungan_karyawan');
    }
    
    public function dokumentasi()
    {
        return $this->hasMany(Dokumentasi::class, 'id_kunjungan', 'id_kunjungan');
    }

    public function notulensi()
    {
        return $this->hasOne(Notulensi::class, 'id_kunjungan', 'id_kunjungan');
    }

    // Helper methods untuk status
    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'pending' => 'Menunggu',
            'approved' => 'Diterima',
            'canceled' => 'Ditolak',
            'done' => 'Selesai',
        ];

        return $statusLabels[$this->status] ?? $this->status;
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isCanceled()
    {
        return $this->status === 'canceled';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }
}