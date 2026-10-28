<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnfermedadRequest;
use App\Http\Requests\UpdateEnfermedadRequest;
use App\Models\TipoEnfermedad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EnfermedadController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $enfermedades = TipoEnfermedad::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('nombre', 'like', "%{$search}%")
                        ->orWhere('codigo_cie10', 'like', "%{$search}%");
                });
            })
            ->withCount('pacientes')
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        $masComun = TipoEnfermedad::withCount('pacientes')
            ->orderByDesc('pacientes_count')
            ->orderBy('nombre')
            ->first();

        return view('dashboard.enfermedades', [
            'enfermedades' => $enfermedades,
            'estadisticas' => [
                'total' => TipoEnfermedad::count(),
                'activas' => TipoEnfermedad::where('es_activo', true)->count(),
                'inactivas' => TipoEnfermedad::where('es_activo', false)->count(),
                'pacientes' => (int) TipoEnfermedad::withCount('pacientes')->get()->sum('pacientes_count'),
                'mas_comun' => $masComun,
            ],
        ]);
    }

    public function show(TipoEnfermedad $enfermedad): TipoEnfermedad
    {
        return $enfermedad->loadCount('pacientes');
    }

    public function store(StoreEnfermedadRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['parametros_control'] = $this->parametrosControl($data['parametros'] ?? []);
        $data['es_activo'] = $data['es_activo'] ?? true;
        unset($data['parametros']);

        $enfermedad = TipoEnfermedad::create($data);

        return redirect()
            ->route('dashboard.enfermedades')
            ->with('success', "Enfermedad \"{$enfermedad->nombre}\" registrada correctamente.");
    }

    public function update(UpdateEnfermedadRequest $request, TipoEnfermedad $enfermedad): RedirectResponse
    {
        $data = $request->validated();
        $data['parametros_control'] = $this->parametrosControl($data['parametros'] ?? []);
        $data['es_activo'] = $data['es_activo'] ?? true;
        unset($data['parametros']);

        $enfermedad->update($data);

        return redirect()
            ->route('dashboard.enfermedades')
            ->with('success', "Enfermedad \"{$enfermedad->nombre}\" actualizada correctamente.");
    }

    public function destroy(TipoEnfermedad $enfermedad): RedirectResponse
    {
        $enAsignados = DB::table('paciente_enfermedades')
            ->where('tipo_enfermedad_id', $enfermedad->id)
            ->exists();

        if ($enAsignados) {
            return redirect()
                ->route('dashboard.enfermedades')
                ->with('error', "No se puede eliminar \"{$enfermedad->nombre}\": tiene pacientes asignados. Desactívala en su lugar.");
        }

        $nombre = $enfermedad->nombre;
        $enfermedad->delete();

        return redirect()
            ->route('dashboard.enfermedades')
            ->with('success', "Enfermedad \"{$nombre}\" eliminada.");
    }

    /**
     * Convierte las filas del formulario en el JSON con formato de los seeders:
     * { "Glucosa": { "min": 70, "max": 180, "unidad": "mg/dL" }, ... }
     *
     * @param  array<int, array<string, mixed>>  $filas
     * @return array<string, mixed>|null
     */
    private function parametrosControl(array $filas): ?array
    {
        $parametros = [];

        foreach ($filas as $fila) {
            $nombre = trim((string) ($fila['nombre'] ?? ''));
            if ($nombre === '') {
                continue;
            }

            $parametros[$nombre] = [
                'min' => isset($fila['min']) && $fila['min'] !== '' ? +$fila['min'] : null,
                'max' => isset($fila['max']) && $fila['max'] !== '' ? +$fila['max'] : null,
                'unidad' => $fila['unidad'] ?? null,
            ];
        }

        return $parametros === [] ? null : $parametros;
    }
}
