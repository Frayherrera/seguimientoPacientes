<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnfermedadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Descartar filas del repetidor que lleguen completamente vacías
        if (is_array($this->parametros)) {
            $this->merge([
                'parametros' => array_values(array_filter(
                    $this->parametros,
                    fn ($fila) => array_filter($fila, fn ($valor) => $valor !== null && $valor !== '') !== []
                )),
            ]);
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100', Rule::unique('tipos_enfermedades', 'nombre')],
            'codigo_cie10' => ['nullable', 'string', 'max:10'],
            'descripcion' => ['nullable', 'string'],
            'es_activo' => ['nullable', 'boolean'],
            'parametros' => ['nullable', 'array'],
            'parametros.*.nombre' => ['required', 'string', 'max:50'],
            'parametros.*.min' => ['nullable', 'numeric'],
            'parametros.*.max' => ['nullable', 'numeric'],
            'parametros.*.unidad' => ['nullable', 'string', 'max:20'],
        ];
    }
}
