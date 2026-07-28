<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['is_active' => 'boolean'];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBendahara(): bool
    {
        return $this->role === 'bendahara';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }
}
