<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Resepsionis extends Authenticatable
{
    use Notifiable;

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

    protected function casts(): array
    {
        return [
            'password_resepsionis' => 'hashed',
        ];
    }

    public function getAuthPassword()
    {
        return $this->password_resepsionis;
    }

    public function getEmailForPasswordReset()
    {
        return $this->email_resepsionis;
    }

    public function getAuthIdentifierName()
    {
        return 'id_resepsionis';
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}
