<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
        protected $table = 'user_profiles';
        
    protected $fillable = [
        'user_id',
        'role_id',
        'identificacion',
        'telefono',
        'dependencia',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
    
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
