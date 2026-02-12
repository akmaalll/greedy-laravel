<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, LogsActivity, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'no_hp',
        'password',
        'role_id',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        // 'password',
        'remember_token',
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
        ];
    }

    /**
     * Relasi ke Pesanan (sebagai Klien)
     */
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'klien_id');
    }

    /**
     * Relasi ke PenugasanFotografer (sebagai Fotografer)
     */
    public function penugasan()
    {
        return $this->hasMany(PenugasanFotografer::class, 'fotografer_id');
    }

    /**
     * Relasi ke KetersediaanFotografer (sebagai Fotografer)
     */
    public function ketersediaan()
    {
        return $this->hasMany(KetersediaanFotografer::class, 'fotografer_id');
    }

    /**
     * Relasi ke RatingFotografer (sebagai Fotografer yang dinilai)
     */
    public function ratingDiterima()
    {
        return $this->hasMany(RatingFotografer::class, 'fotografer_id');
    }

    /**
     * Relasi ke RatingFotografer (sebagai Klien yang memberi rating)
     */
    public function ratingDiberikan()
    {
        return $this->hasMany(RatingFotografer::class, 'klien_id');
    }

    /**
     * Relasi ke RatingLayanan (sebagai Klien yang memberi rating)
     */
    public function ratingLayanan()
    {
        return $this->hasMany(RatingLayanan::class, 'klien_id');
    }

    public function scopeWhereRole($query, $role)
    {
        if (is_int($role)) {
            return $query->where('role_id', $role);
        }

        return $query->whereHas('role', function ($q) use ($role) {
            $q->where('slug', $role);
        });
    }

    public function hasPermission($menuSlug, $action)
    {
        if (! $this->role) {
            return false;
        }

        $menu = $this->role->menus()->where('menus.slug', $menuSlug)->first();
        if (! $menu) {
            return false;
        }

        return (bool) $menu->pivot->{"can_{$action}"};
    }
}
