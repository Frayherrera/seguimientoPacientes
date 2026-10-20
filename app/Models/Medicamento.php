<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'principio_activo',
        'forma_farmaceutica',
        'concentracion',
        'laboratorio',
        'es_controlado',
        'es_activo',
    ];

    protected $casts = [
        'es_controlado' => 'boolean',
        'es_activo' => 'boolean',
    ];

    public function prescripciones()
    {
        return $this->hasMany(Prescripcion::class);
    }
}
