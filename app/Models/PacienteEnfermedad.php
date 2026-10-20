<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacienteEnfermedad extends Model
{
    use HasFactory;

    protected $table = 'paciente_enfermedades';

    protected $fillable = [
        'paciente_id',
        'tipo_enfermedad_id',
        'fecha_diagnostico',
        'estado',
        'observaciones',
        'es_principal',
    ];

    protected $casts = [
        'fecha_diagnostico' => 'date',
        'es_principal' => 'boolean',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function tipoEnfermedad()
    {
        return $this->belongsTo(TipoEnfermedad::class);
    }
}
