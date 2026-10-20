<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescripcion extends Model
{
    use HasFactory;

    protected $fillable = [
        'historia_clinica_id',
        'paciente_id',
        'medicamento_id',
        'dosis',
        'frecuencia',
        'via_administracion',
        'duracion_dias',
        'indicaciones_especiales',
        'fecha_inicio',
        'fecha_fin',
        'es_activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'es_activo' => 'boolean',
    ];

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class);
    }
}
