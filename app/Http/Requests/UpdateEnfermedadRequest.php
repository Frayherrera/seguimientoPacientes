<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateEnfermedadRequest extends StoreEnfermedadRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['nombre'] = [
            'required',
            'string',
            'max:100',
            Rule::unique('tipos_enfermedades', 'nombre')->ignore($this->route('enfermedad')),
        ];

        return $rules;
    }
}
