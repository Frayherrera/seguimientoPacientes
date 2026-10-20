<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoEnfermedad extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipos_enfermedades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo_cie10',
        'parametros_control',
        'es_activo',
    ];

    protected $casts = [
        'parametros_control' => 'array',
        'es_activo' => 'boolean',
    ];

    public function pacientes()
    {
        return $this->belongsToMany(Paciente::class, 'paciente_enfermedades')
                    ->withPivot(['fecha_diagnostico', 'estado', 'es_principal']);
    }

    public function configuracionAlertas()
    {
        return $this->hasMany(ConfiguracionAlerta::class);
    }
}
