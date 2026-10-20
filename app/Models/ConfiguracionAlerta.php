<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionAlerta extends Model
{
    use HasFactory;

    protected $table = 'configuracion_alertas';

    protected $fillable = [
        'tipo_enfermedad_id',
        'nombre_parametro',
        'valor_minimo',
        'valor_maximo',
        'unidad',
        'nivel_alerta',
        'mensaje_alerta',
        'es_activo',
    ];

    protected $casts = [
        'valor_minimo' => 'decimal:2',
        'valor_maximo' => 'decimal:2',
        'es_activo' => 'boolean',
    ];

    public function tipoEnfermedad()
    {
        return $this->belongsTo(TipoEnfermedad::class);
    }
}
