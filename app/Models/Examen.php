<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    use HasFactory;

    protected $table = 'examenes';

    protected $fillable = [
        'historia_clinica_id',
        'paciente_id',
        'tipo_examen',
        'nombre_examen',
        'descripcion',
        'fecha_solicitud',
        'fecha_realizacion',
        'laboratorio',
        'estado',
        'resultados',
        'observaciones_resultado',
        'archivo_resultado',
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'fecha_realizacion' => 'date',
    ];

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'Solicitado');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', 'Completado');
    }
}
