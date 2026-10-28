<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePacienteRequest extends FormRequest
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
            'documento_identidad' => ['required', 'string', 'max:20', Rule::unique('pacientes', 'documento_identidad')],
            'tipo_documento' => ['required', Rule::in(['CC', 'CE', 'TI', 'PAS'])],
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'genero' => ['required', Rule::in(['M', 'F', 'Otro'])],
            'telefono' => ['nullable', 'string', 'max:20'],
            'celular' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'departamento' => ['nullable', 'string', 'max:100'],
            'eps' => ['nullable', 'string', 'max:150'],
            'ips_primaria' => ['nullable', 'string', 'max:150'],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo', 'Fallecido'])],
            'observaciones' => ['nullable', 'string'],
            'enfermedad_id' => ['nullable', 'integer', Rule::exists('tipos_enfermedades', 'id')->where('es_activo', true)],
            'fecha_diagnostico' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }
}
