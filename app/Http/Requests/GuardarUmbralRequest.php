<?php

namespace App\Http\Requests;

use App\Services\GeneradorDeAlertas;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarUmbralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'tipo_enfermedad_id' => [
                'required',
                'integer',
                Rule::exists('tipos_enfermedades', 'id')->where('es_activo', true),
            ],
            'nombre_parametro' => ['required', 'string', Rule::in(GeneradorDeAlertas::PARAMETROS)],
            'valor_minimo' => ['nullable', 'numeric'],
            'valor_maximo' => ['nullable', 'numeric'],
            'unidad' => ['required', 'string', 'max:20'],
            // La tabla tiene unique(tipo_enfermedad_id, nombre_parametro, nivel_alerta)
            'nivel_alerta' => [
                'required',
                Rule::in(['Bajo', 'Medio', 'Alto']),
                Rule::unique('configuracion_alertas', 'nivel_alerta')
                    ->where('tipo_enfermedad_id', $this->input('tipo_enfermedad_id'))
                    ->where('nombre_parametro', $this->input('nombre_parametro'))
                    ->ignore($this->route('configuracionAlerta')?->id),
            ],
            'mensaje_alerta' => ['nullable', 'string', 'max:500'],
            'es_activo' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $minimo = $this->input('valor_minimo');
            $maximo = $this->input('valor_maximo');

            if (is_numeric($minimo) && is_numeric($maximo) && (float) $minimo > (float) $maximo) {
                $validator->errors()->add(
                    'valor_maximo',
                    'El valor máximo debe ser mayor o igual al valor mínimo.'
                );
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function datosUmbral(): array
    {
        $datos = $this->safe()->only([
            'tipo_enfermedad_id',
            'nombre_parametro',
            'valor_minimo',
            'valor_maximo',
            'unidad',
            'nivel_alerta',
            'mensaje_alerta',
        ]);

        $datos['es_activo'] = $this->boolean('es_activo');

        return $datos;
    }
}
