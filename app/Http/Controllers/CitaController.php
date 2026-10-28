<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CitaController extends Controller
{
    /** Estados desde los cuales una cita todavía se puede editar */
    private const ESTADOS_EDITABLES = ['Programada', 'Confirmada'];

    /** Transiciones de estado permitidas */
    private const TRANSICIONES = [
        'Programada' => ['Confirmada', 'Cancelada', 'No Presentado'],
        'Confirmada' => ['Atendida', 'Cancelada', 'No Presentado'],
    ];

    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', Rule::in(['Programada', 'Confirmada', 'Atendida', 'Cancelada', 'No Presentado'])],
            'medico_id' => ['nullable', 'integer', 'exists:users,id'],
            'periodo' => ['nullable', 'string', Rule::in(['proximas', 'hoy', 'pasadas', 'todas'])],
        ]);

        $periodo = $filtros['periodo'] ?? 'proximas';

        $citas = Cita::query()
            ->when($filtros['search'] ?? null, function ($query, $search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('motivo', 'like', "%{$search}%")
                        ->orWhereHas('paciente', function ($paciente) use ($search) {
                            $paciente->where('nombres', 'like', "%{$search}%")
                                ->orWhere('apellidos', 'like', "%{$search}%")
                                ->orWhere('documento_identidad', 'like', "%{$search}%");
                        })
                        ->orWhereHas('medico', function ($medico) use ($search) {
                            $medico->where('nombre', 'like', "%{$search}%")
                                ->orWhere('apellidos', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filtros['estado'] ?? null, fn ($query, $estado) => $query->where('estado', $estado))
            ->when($filtros['medico_id'] ?? null, fn ($query, $medicoId) => $query->where('medico_id', $medicoId))
            ->when($periodo === 'proximas', fn ($query) => $query->whereDate('fecha_cita', '>=', now()->toDateString()))
            ->when($periodo === 'hoy', fn ($query) => $query->whereDate('fecha_cita', now()->toDateString()))
            ->when($periodo === 'pasadas', fn ($query) => $query->whereDate('fecha_cita', '<', now()->toDateString()))
            ->with(['paciente', 'medico'])
            ->orderBy('fecha_cita', $periodo === 'pasadas' ? 'desc' : 'asc')
            ->orderBy('hora_inicio')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.citas', [
            'citas' => $citas,
            'pacientesActivos' => Paciente::activos()->orderBy('apellidos')->get(),
            'medicos' => User::where('es_activo', true)
                ->whereHas('role', fn ($query) => $query->where('nombre', 'Médico'))
                ->orderBy('apellidos')
                ->get(),
            'filtros' => $filtros,
            'estadisticas' => [
                'hoy' => Cita::whereDate('fecha_cita', now()->toDateString())->count(),
                'ayer' => Cita::whereDate('fecha_cita', now()->subDay()->toDateString())->count(),
                'pendientes' => Cita::whereIn('estado', self::ESTADOS_EDITABLES)
                    ->whereDate('fecha_cita', '>=', now()->toDateString())
                    ->count(),
                'atendidas' => Cita::where('estado', 'Atendida')->count(),
                'agenda_hoy' => [
                    'atendidas' => Cita::whereDate('fecha_cita', now()->toDateString())
                        ->where('estado', 'Atendida')->count(),
                    'pendientes' => Cita::whereDate('fecha_cita', now()->toDateString())
                        ->whereIn('estado', self::ESTADOS_EDITABLES)->count(),
                    'otras' => Cita::whereDate('fecha_cita', now()->toDateString())
                        ->whereIn('estado', ['Cancelada', 'No Presentado'])->count(),
                ],
            ],
        ]);
    }

    public function store(StoreCitaRequest $request): RedirectResponse
    {
        Cita::create($request->validated() + [
            'estado' => 'Programada',
            'recordatorio_enviado' => false,
        ]);

        return redirect()
            ->route('dashboard.citas')
            ->with('success', 'Cita programada exitosamente.');
    }

    public function update(UpdateCitaRequest $request, Cita $cita): RedirectResponse
    {
        if (! in_array($cita->estado, self::ESTADOS_EDITABLES)) {
            return redirect()
                ->route('dashboard.citas')
                ->with('error', "Solo se pueden editar citas en estado Programada o Confirmada (la cita está «{$cita->estado}»).");
        }

        $cita->update($request->validated());

        return redirect()
            ->route('dashboard.citas')
            ->with('success', 'Cita actualizada exitosamente.');
    }

    public function cambiarEstado(Request $request, Cita $cita): RedirectResponse
    {
        $data = $request->validate([
            'estado' => ['required', Rule::in(['Confirmada', 'Atendida', 'Cancelada', 'No Presentado'])],
        ]);

        $permitidos = self::TRANSICIONES[$cita->estado] ?? [];

        if (! in_array($data['estado'], $permitidos)) {
            return redirect()
                ->route('dashboard.citas')
                ->with('error', "No se puede cambiar una cita «{$cita->estado}» a «{$data['estado']}».");
        }

        $cita->update($data);

        return redirect()
            ->route('dashboard.citas')
            ->with('success', "Cita marcada como {$data['estado']}.");
    }
}
