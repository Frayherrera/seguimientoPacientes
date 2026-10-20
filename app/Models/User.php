<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'role_id',
        'nombre',
        'apellidos',
        'email',
        'telefono',
        'especialidad',
        'registro_medico',
        'password',
        'es_activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'es_activo' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function historiasClinicas()
    {
        return $this->hasMany(HistoriaClinica::class, 'medico_id');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'medico_id');
    }

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellidos}";
    }
}
