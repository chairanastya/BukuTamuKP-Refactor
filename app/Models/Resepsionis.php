<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class Resepsionis extends Model implements Authenticatable
{
    use AuthenticatableTrait;

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

    public function getAuthPassword()
    {
        return $this->password_resepsionis;
    }

    public function getEmailForPasswordReset()
    {
        return $this->email_resepsionis;
    }

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}
