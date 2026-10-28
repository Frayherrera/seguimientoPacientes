<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActualizarPerfilRequest extends FormRequest
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
            'nombre' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'especialidad' => ['nullable', 'string', 'max:100'],
            'registro_medico' => ['nullable', 'string', 'max:50'],
            // Cambiar la contraseña es opcional: vacía = mantener la actual.
            // Si se envía una nueva, hay que confirmar la contraseña actual.
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_actual' => ['nullable', 'current_password'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('password') && ! $this->filled('password_actual')) {
                $validator->errors()->add(
                    'password_actual',
                    'Confirma tu contraseña actual para cambiar la contraseña.'
                );
            }
        });
    }
}
