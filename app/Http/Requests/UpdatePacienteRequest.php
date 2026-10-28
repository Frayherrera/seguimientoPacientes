<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdatePacienteRequest extends StorePacienteRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['documento_identidad'] = [
            'required',
            'string',
            'max:20',
            Rule::unique('pacientes', 'documento_identidad')->ignore($this->route('paciente')),
        ];

        return $rules;
    }
}
