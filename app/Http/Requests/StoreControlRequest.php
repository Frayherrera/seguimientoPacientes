<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreControlRequest extends FormRequest
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
            'paciente_id' => [
                'required',
                'integer',
                Rule::exists('pacientes', 'id')->whereNull('deleted_at'),
            ],
            'fecha_atencion' => ['required', 'date', 'before_or_equal:today'],
            'hora_atencion' => ['required', 'date_format:H:i'],
            'tipo_atencion' => ['required', Rule::in(['Control', 'Urgencias', 'Primera vez'])],
            'motivo_consulta' => ['required', 'string', 'max:1000'],
            'enfermedad_actual' => ['nullable', 'string'],
            'antecedentes' => ['nullable', 'string'],
            'examen_fisico' => ['nullable', 'string'],
            'impresion_diagnostica' => ['required', 'string', 'max:1000'],
            'plan_manipulacion' => ['nullable', 'string'],
            'indicaciones' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['Abierta', 'Cerrada', 'En seguimiento'])],

            // Signos vitales (todos opcionales)
            'presion_sistolica' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'presion_diastolica' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'frecuencia_cardiaca' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'frecuencia_respiratoria' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'temperatura' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'peso' => ['nullable', 'numeric', 'min:0.5', 'max:500'], // kg
            'talla' => ['nullable', 'numeric', 'min:30', 'max:250'], // cm
            'glucosa' => ['nullable', 'numeric', 'min:0', 'max:2000'],
            'saturacion_oxigeno' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /**
     * La columna signos_vitales.imc es decimal(5,2) (máx. 999.99): una
     * combinación de peso/talla que produzca un IMC mayor se rechaza con un
     * error claro en vez de fallar el INSERT en MySQL.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $peso = $this->input('peso');
            $talla = $this->input('talla');

            if (! is_numeric($peso) || ! is_numeric($talla) || (float) $talla <= 0) {
                return;
            }

            $imc = (float) $peso / (((float) $talla / 100) ** 2);

            if ($imc > 999.99) {
                $validator->errors()->add(
                    'talla',
                    'La combinación de peso y talla produce un IMC fuera de rango permitido; verifique los valores.'
                );
            }
        });
    }

    /**
     * @return array<int, string>
     */
    public function signosVitales(): array
    {
        return [
            'presion_sistolica',
            'presion_diastolica',
            'frecuencia_cardiaca',
            'frecuencia_respiratoria',
            'temperatura',
            'peso',
            'talla',
            'glucosa',
            'saturacion_oxigeno',
        ];
    }
}
