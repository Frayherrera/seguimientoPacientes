<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends StoreUsuarioRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['email'] = [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($this->route('usuario')),
        ];

        // En edición la contraseña es opcional: vacío mantiene la actual
        $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];

        return $rules;
    }
}
