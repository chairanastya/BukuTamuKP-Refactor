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
    ];

    /**
     * Boot the model and auto-generate access token
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tamu) {
            if (empty($tamu->ktp_access_token)) {
                $tamu->ktp_access_token = Str::random(64);
            }
        });
    }

    // Relationships

    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class, 'id_tamu', 'id_tamu');
    }
}
