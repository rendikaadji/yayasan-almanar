<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'bidang_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the bidang associated with the user.
     */
    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    /**
     * Check if the user is a field treasurer (bendahara bidang).
     */
    public function isBendaharaBidang(): bool
    {
        return $this->role === 'bendahara_bidang' && !is_null($this->bidang_id);
    }

    /**
     * Check if the user is a general treasurer (bendahara umum).
     */
    public function isBendaharaUmum(): bool
    {
        return $this->role === 'bendahara_umum';
    }

    /**
     * Check if the user is an admin or ketua yayasan.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
