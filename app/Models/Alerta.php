<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'signo_vital_id',
        'historia_clinica_id',
        'tipo_alerta',
        'parametro',
        'valor_detectado',
        'valor_referencia_min',
        'valor_referencia_max',
        'nivel_severidad',
        'mensaje',
        'es_leida',
        'fecha_lectura',
        'usuario_lectura_id',
        'observaciones_atencion',
    ];

    protected $casts = [
        'valor_detectado' => 'decimal:2',
        'valor_referencia_min' => 'decimal:2',
        'valor_referencia_max' => 'decimal:2',
        'es_leida' => 'boolean',
        'fecha_lectura' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function signoVital()
    {
        return $this->belongsTo(SignoVital::class);
    }

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class);
    }

    public function usuarioLectura()
    {
        return $this->belongsTo(User::class, 'usuario_lectura_id');
    }

    public function scopeNoLeidas($query)
    {
        return $query->where('es_leida', false);
    }

    public function scopeCriticas($query)
    {
        return $query->where('nivel_severidad', 'Crítico');
    }
}
