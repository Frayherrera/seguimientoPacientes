<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignoVital extends Model
{
    use HasFactory;

    protected $table = 'signos_vitales';

    protected $fillable = [
        'historia_clinica_id',
        'paciente_id',
        'presion_sistolica',
        'presion_diastolica',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'temperatura',
        'peso',
        'talla',
        'imc',
        'glucosa',
        'saturacion_oxigeno',
    ];

    protected $casts = [
        'presion_sistolica' => 'decimal:2',
        'presion_diastolica' => 'decimal:2',
        'frecuencia_cardiaca' => 'decimal:2',
        'frecuencia_respiratoria' => 'decimal:2',
        'temperatura' => 'decimal:2',
        'peso' => 'decimal:2',
        'talla' => 'decimal:2',
        'imc' => 'decimal:2',
        'glucosa' => 'decimal:2',
        'saturacion_oxigeno' => 'decimal:2',
    ];

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    protected static function booted()
    {
        // Calcular IMC automáticamente
        static::saving(function ($signo) {
            if ($signo->peso && $signo->talla && $signo->talla > 0) {
                $tallaMetros = $signo->talla / 100;
                $imc = round($signo->peso / ($tallaMetros * $tallaMetros), 2);

                // La columna imc es decimal(5,2) (máx. 999.99): no asignar
                // valores que la excedan (MySQL rechazaría el INSERT)
                $signo->imc = $imc <= 999.99 ? $imc : null;
            }
        });
    }
}
