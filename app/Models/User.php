<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'wajib_ganti_password',
    'aktif',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_PESERTA = 'peserta';

    /**
     * [role => label tampilan]
     */
    public const ROLES = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_PESERTA => 'Peserta Magang',
    ];

    /**
     * Sama dengan default kolom, supaya model baru langsung punya nilainya.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'wajib_ganti_password' => false,
        'aktif' => true,
    ];

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
            'wajib_ganti_password' => 'boolean',
            'aktif' => 'boolean',
        ];
    }

    /**
     * Data peserta magang milik akun ini (khusus role peserta).
     */
    public function pesertaMagang(): HasOne
    {
        return $this->hasOne(PesertaMagang::class);
    }

    public function isPeserta(): bool
    {
        return $this->role === self::ROLE_PESERTA;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function labelRole(): string
    {
        return self::ROLES[$this->role] ?? 'Admin';
    }

    /**
     * Nama route dashboard sesuai role, atau null bila role tidak dikenal.
     */
    public function routeDashboard(): ?string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'superadmin.dashboard',
            self::ROLE_ADMIN => 'admin.dashboard',
            self::ROLE_PESERTA => 'peserta.dashboard',
            default => null,
        };
    }

    /**
     * Huruf pertama nama, dipakai bila belum ada foto profil.
     */
    public function inisial(): string
    {
        return mb_strtoupper(mb_substr(trim((string) $this->name) ?: 'A', 0, 1));
    }

    /**
     * URL foto profil (lewat controller berizin), atau null bila belum ada.
     * Nama file ikut di query agar cache browser berganti saat foto diganti.
     */
    public function urlFoto(): ?string
    {
        return $this->foto_path
            ? route('foto-profil.show', [$this, 'v' => substr(md5($this->foto_path), 0, 8)])
            : null;
    }
}
