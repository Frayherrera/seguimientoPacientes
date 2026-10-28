<?php

namespace App\Http\Requests;

use App\Models\Cita;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCitaRequest extends FormRequest
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
            'medico_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('es_activo', true),
            ],
            'fecha_cita' => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['nullable', 'date_format:H:i', 'after:hora_inicio'],
            'tipo_cita' => ['required', Rule::in(['Control', 'Urgencia', 'Primera vez', 'Teleconsulta'])],
            'consultorio' => ['nullable', 'string', 'max:20'],
            'motivo' => ['required', 'string', 'max:500'],
            'observaciones' => ['nullable', 'string'],
        ];
    }

    /**
     * El médico no puede tener dos citas en la misma fecha y hora
     * (salvo que las anteriores estén canceladas o sin presentar).
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $medicoId = $this->input('medico_id');
            $fecha = $this->input('fecha_cita');
            $hora = $this->input('hora_inicio');

            if (! $medicoId || ! $fecha || ! $hora) {
                return;
            }

            $conflicto = Cita::query()
                ->where('medico_id', $medicoId)
                ->whereDate('fecha_cita', $fecha)
                ->where('hora_inicio', 'like', $hora.'%')
                ->whereIn('estado', ['Programada', 'Confirmada'])
                ->when($this->route('cita'), fn ($query, $cita) => $query->where('id', '!=', $cita->id))
                ->exists();

            if ($conflicto) {
                $validator->errors()->add(
                    'hora_inicio',
                    'El médico ya tiene una cita programada a esa fecha y hora.'
                );
            }
        });
    }
}
