<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Dokumentasi extends Model
{
    protected $table = 'dokumentasi';
    protected $primaryKey = 'id_dokumentasi';

    public $timestamps = false;

    protected $fillable = [
        'id_kunjungan',
        'dokumentasi_public_id',
        'dokumentasi_url',
        'access_token',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /**
     * Boot the model and auto-generate access token
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dokumentasi) {
            if (empty($dokumentasi->access_token)) {
                $dokumentasi->access_token = Str::random(64);
            }
        });
    }

    // Relationships
    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'id_kunjungan', 'id_kunjungan');
    }
}
