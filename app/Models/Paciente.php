<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'documento_identidad',
        'tipo_documento',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'genero',
        'telefono',
        'celular',
        'email',
        'direccion',
        'ciudad',
        'departamento',
        'eps',
        'ips_primaria',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function enfermedades()
    {
        return $this->belongsToMany(TipoEnfermedad::class, 'paciente_enfermedades')
                    ->withPivot(['fecha_diagnostico', 'estado', 'es_principal']);
    }

    public function historiasClinicas()
    {
        return $this->hasMany(HistoriaClinica::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function signosVitales()
    {
        return $this->hasMany(SignoVital::class);
    }

    public function prescripciones()
    {
        return $this->hasMany(Prescripcion::class);
    }

    public function examenes()
    {
        return $this->hasMany(Examen::class);
    }

    public function alertas()
    {
        return $this->hasMany(Alerta::class);
    }

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    public function getEdadAttribute()
    {
        return $this->fecha_nacimiento ? $this->fecha_nacimiento->age : null;
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'Activo');
    }
}
