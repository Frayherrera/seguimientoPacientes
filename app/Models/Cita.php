<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'fecha_cita',
        'hora_inicio',
        'hora_fin',
        'tipo_cita',
        'consultorio',
        'estado',
        'motivo',
        'observaciones',
        'recordatorio_enviado',
        'fecha_recordatorio',
    ];

    protected $casts = [
        'fecha_cita' => 'date',
        'recordatorio_enviado' => 'boolean',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_cita', today());
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['Programada', 'Confirmada']);
    }

    public function scopeDelMedico($query, $medicoId)
    {
        return $query->where('medico_id', $medicoId);
    }
}
