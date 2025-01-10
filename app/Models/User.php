<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null ;
    }

    protected static function booted(): void
    {
        // Evento para cuando se elimina un usuario
        static::deleting(function (self $user) {
            // Verificar y eliminar el archivo del avatar antes de eliminar el modelo
            if ($user->avatar_url) {
                $filePath = 'public/avatar/' . basename($user->avatar_url);
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }
            }

            // Dejar avatar_url en null antes de eliminar el modelo (opcional, ya que el modelo será eliminado)
            $user->avatar_url = null;
            $user->save();
        });

        // Evento para cuando se actualiza el modelo (como el avatar)
        static::updating(function (self $user) {
            // Verificar si avatar_url ha cambiado
            if ($user->isDirty('avatar_url')) {
                // Eliminar el archivo anterior si avatar_url está siendo actualizado
                $originalAvatarUrl = $user->getOriginal('avatar_url');
                if ($originalAvatarUrl) {
                    $originalFilePath = 'public/avatar/' . basename($originalAvatarUrl);
                    if (Storage::exists($originalFilePath)) {
                        Storage::delete($originalFilePath);
                    }
                }
            }
        });
    }

    public function trabajos(): BelongsToMany
    {
        return $this->belongsToMany(Trabajo::class, 'trabajo_mecanicos', 'mecanico_id', 'trabajo_id');
    }
}
