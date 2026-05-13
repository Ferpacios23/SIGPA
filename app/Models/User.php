<?php
 
namespace App\Models;
 
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
 
class User extends Authenticatable
{
    use HasFactory, Notifiable;
 
    protected $fillable = ['name', 'email', 'password', 'must_change_password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'     => 'datetime',
            'password'              => 'hashed',
            'must_change_password'  => 'boolean',
        ];
    }
 
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // ── Relaciones ────────────────────────────────────────────────
 
    /** Perfil extendido del usuario (contiene role_id) */
    public function profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserProfile::class);
    }
 
    /** Acceso directo al rol a través del perfil */
    public function role(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            Role::class,        // Modelo final
            UserProfile::class, // Modelo intermedio
            'user_id',          // FK en user_profiles → users
            'id',               // PK en roles
            'id',               // PK en users
            'role_id'           // FK en user_profiles → roles
        );
    }
 
    /** Préstamos de aulas solicitados por este usuario */
    public function prestamosAulas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrestamoAula::class);
    }
 
    /** Préstamos de aulas que este usuario aprobó */
    public function prestamosAprobados(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrestamoAula::class, 'aprobado_por');
    }
 
    // ── Helpers ───────────────────────────────────────────────────
 
    /** Retorna el slug del rol o null si no tiene perfil/rol */
    public function getRoleSlugAttribute(): ?string
    {
        return $this->profile?->role?->slug;
    }
 
    /** Verifica si el usuario tiene un slug de rol específico */
    public function hasRole(string $slug): bool
    {
        return $this->roleSlug === $slug;
    }
 
    /** Verifica si el usuario tiene alguno de los slugs dados */
    public function hasAnyRole(array $slugs): bool
    {
        return in_array($this->roleSlug, $slugs);
    }
}
 