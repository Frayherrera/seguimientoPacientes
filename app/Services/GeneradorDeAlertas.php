<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\ConfiguracionAlerta;
use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\SignoVital;

/**
 * Evalúa los signos vitales de un control contra la configuración de alertas
 * de las enfermedades del paciente y genera las alertas correspondientes.
 */
class GeneradorDeAlertas
{
    public const TIPO = 'Valor fuera de rango';

    /** Columnas de signos_vitales que pueden evaluarse contra configuracion_alertas.nombre_parametro */
    public const PARAMETROS = [
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

    private const ORDEN_SEVERIDAD = ['Bajo' => 0, 'Medio' => 1, 'Alto' => 2];

    private const MAPA_SEVERIDAD = [
        'Bajo' => 'Info',
        'Medio' => 'Advertencia',
        'Alto' => 'Crítico',
    ];

    /**
     * Regenera las alertas automáticas de una historia clínica:
     * primero borra las previas de este control y luego crea una por cada
     * parámetro fuera de rango (la de mayor severidad si varias gatillan).
     *
     * @return int Cantidad de alertas generadas.
     */
    public function regenerar(Paciente $paciente, HistoriaClinica $historia, ?SignoVital $signos): int
    {
        Alerta::where('historia_clinica_id', $historia->id)
            ->where('tipo_alerta', self::TIPO)
            ->delete();

        if (! $signos) {
            return 0;
        }

        $valores = [];
        foreach (self::PARAMETROS as $parametro) {
            $valor = $signos->{$parametro};
            if ($valor !== null) {
                $valores[$parametro] = $valor;
            }
        }

        if ($valores === []) {
            return 0;
        }

        $configuraciones = ConfiguracionAlerta::whereIn(
            'tipo_enfermedad_id',
            $paciente->enfermedades()->pluck('tipos_enfermedades.id')
        )
            ->where('es_activo', true)
            ->get()
            ->groupBy('nombre_parametro');

        $generadas = 0;

        foreach ($valores as $parametro => $valor) {
            if (! isset($configuraciones[$parametro])) {
                continue;
            }

            $gatilladas = $configuraciones[$parametro]
                ->filter(fn (ConfiguracionAlerta $config) => $this->fueraDeRango((float) $valor, $config));

            if ($gatilladas->isEmpty()) {
                continue;
            }

            $config = $gatilladas
                ->sortByDesc(fn (ConfiguracionAlerta $c) => self::ORDEN_SEVERIDAD[$c->nivel_alerta] ?? 0)
                ->first();

            Alerta::create([
                'paciente_id' => $paciente->id,
                'signo_vital_id' => $signos->id,
                'historia_clinica_id' => $historia->id,
                'tipo_alerta' => self::TIPO,
                'parametro' => $parametro,
                'valor_detectado' => $valor,
                'valor_referencia_min' => $config->valor_minimo !== null ? (float) $config->valor_minimo : null,
                'valor_referencia_max' => $config->valor_maximo !== null ? (float) $config->valor_maximo : null,
                'nivel_severidad' => self::MAPA_SEVERIDAD[$config->nivel_alerta] ?? 'Advertencia',
                'mensaje' => $config->mensaje_alerta
                    ?? sprintf('%s fuera del rango de referencia (%s %s – %s)', $parametro, $config->valor_minimo ?? '-', $config->valor_maximo ?? '-', $config->unidad),
            ]);

            $generadas++;
        }

        return $generadas;
    }

    private function fueraDeRango(float $valor, ConfiguracionAlerta $config): bool
    {
        if ($config->valor_minimo !== null && $valor < (float) $config->valor_minimo) {
            return true;
        }

        if ($config->valor_maximo !== null && $valor > (float) $config->valor_maximo) {
            return true;
        }

        return false;
    }
}
