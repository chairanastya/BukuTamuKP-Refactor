<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tamu extends Model
{
    protected $table = 'tamu';
    protected $primaryKey = 'id_tamu';
    protected $fillable = [
        'nama_tamu',
        'email_tamu',
        'instansi_tamu',
        'ktp_public_id',
        'ktp_access_token',
        'ktp_token_expired_at',
    ];

    protected $casts = [
        'ktp_token_expired_at' => 'datetime',
    ];

    /**
     * Boot the model and auto-generate access token
     * Token tidak expired karena hanya digunakan oleh resepsionis authenticated
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tamu) {
            if (empty($tamu->ktp_access_token)) {
                $tamu->ktp_access_token = Str::random(64);
                // Tidak set expiry - token permanent untuk resepsionis authenticated
            }
        });
    }

    // Relationships

    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class, 'id_tamu', 'id_tamu');
    }
}
