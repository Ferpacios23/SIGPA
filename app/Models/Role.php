<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Role extends Model
{
    protected $table = 'roles';
 
    protected $fillable = ['nombre', 'slug', 'descripcion', 'activo'];
 
    protected $casts = ['activo' => 'boolean'];
 
    public function profiles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserProfile::class);
    }
 
    public function users(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            UserProfile::class,
            'role_id',  // FK en user_profiles → roles
            'id',       // PK en users
            'id',       // PK en roles
            'user_id'   // FK en user_profiles → users
        );
    }
}