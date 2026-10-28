<?php

namespace App\Http\Requests;

class UpdateControlRequest extends StoreControlRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        // El paciente de una historia clínica es inmutable: no se valida
        // ni se acepta cambio (el controller lo fuerza al valor original).
        unset($rules['paciente_id']);

        return $rules;
    }
}
