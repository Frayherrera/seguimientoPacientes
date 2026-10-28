<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePacienteRequest;
use App\Http\Requests\UpdatePacienteRequest;
use App\Models\Paciente;
use App\Models\TipoEnfermedad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PacienteController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'in:Activo,Inactivo,Fallecido'],
        ]);

        $pacientes = Paciente::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('nombres', 'like', "%{$search}%")
                        ->orWhere('apellidos', 'like', "%{$search}%")
                        ->orWhere('documento_identidad', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['estado'] ?? null, fn ($query, $estado) => $query->where('estado', $estado))
            ->with('enfermedades')
            ->withMax('historiasClinicas', 'fecha_atencion')
            ->orderBy('apellidos')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.pacientes', [
            'pacientes' => $pacientes,
            'enfermedades' => TipoEnfermedad::where('es_activo', true)->orderBy('nombre')->get(),
            'estadisticas' => [
                'total' => Paciente::count(),
                'activos' => Paciente::where('estado', 'Activo')->count(),
                'inactivos' => Paciente::where('estado', 'Inactivo')->count(),
            ],
        ]);
    }

    public function show(Paciente $paciente): Paciente
    {
        return $paciente->load([
            'enfermedades',
            'historiasClinicas' => fn ($query) => $query->latest('fecha_atencion')->limit(1),
        ]);
    }

    public function store(StorePacienteRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $enfermedadId = $data['enfermedad_id'] ?? null;
        $fechaDiagnostico = $data['fecha_diagnostico'] ?? now()->toDateString();
        unset($data['enfermedad_id'], $data['fecha_diagnostico']);

        $paciente = Paciente::create($data);

        if ($enfermedadId) {
            $paciente->enfermedades()->attach($enfermedadId, [
                'fecha_diagnostico' => $fechaDiagnostico,
                'estado' => 'Activo',
                'es_principal' => true,
            ]);
        }

        return redirect()
            ->route('dashboard.pacientes')
            ->with('success', "Paciente {$paciente->nombre_completo} registrado correctamente.");
    }

    public function update(UpdatePacienteRequest $request, Paciente $paciente): RedirectResponse
    {
        $data = $request->validated();
        $enfermedadId = $data['enfermedad_id'] ?? null;
        $fechaDiagnostico = $data['fecha_diagnostico'] ?? now()->toDateString();
        unset($data['enfermedad_id'], $data['fecha_diagnostico']);

        $paciente->update($data);

        if ($enfermedadId) {
            $paciente->enfermedades()->syncWithoutDetaching([
                $enfermedadId => [
                    'fecha_diagnostico' => $fechaDiagnostico,
                    'estado' => 'Activo',
                    'es_principal' => $paciente->enfermedades()->doesntExist(),
                ],
            ]);
        }

        return redirect()
            ->route('dashboard.pacientes')
            ->with('success', "Paciente {$paciente->nombre_completo} actualizado correctamente.");
    }

    public function destroy(Paciente $paciente): RedirectResponse
    {
        $nombre = $paciente->nombre_completo;
        $paciente->delete();

        return redirect()
            ->route('dashboard.pacientes')
            ->with('success', "Paciente {$nombre} eliminado.");
    }
}
