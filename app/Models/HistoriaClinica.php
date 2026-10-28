<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriaClinica extends Model
{
    use HasFactory;

    protected $table = 'historias_clinicas';

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'fecha_atencion',
        'hora_atencion',
        'tipo_atencion',
        'motivo_consulta',
        'enfermedad_actual',
        'antecedentes',
        'examen_fisico',
        'impresion_diagnostica',
        'plan_manipulacion',
        'indicaciones',
        'estado',
    ];

    protected $casts = [
        'fecha_atencion' => 'date',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
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
}
