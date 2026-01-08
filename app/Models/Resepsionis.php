<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resepsionis extends Model
{
    protected $table = 'resepsionis';
    protected $primaryKey = 'id_resepsionis';

    protected $fillable = [
        'id_karyawan',
        'nama_resepsionis',
        'email_resepsionis',
        'password_resepsionis',
    ];

    protected $hidden = [
        'password_resepsionis',
    ];

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}
