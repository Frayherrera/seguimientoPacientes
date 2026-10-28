<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreControlRequest;
use App\Http\Requests\UpdateControlRequest;
use App\Models\Alerta;
use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\SignoVital;
use App\Services\GeneradorDeAlertas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeguimientoController extends Controller
{
    public function __construct(private GeneradorDeAlertas $generadorDeAlertas) {}

    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'in:Al día,Pendiente,Urgente'],
        ]);

        $pacientes = $this->baseQuery()
            ->when($filtros['search'] ?? null, function ($query, $search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('nombres', 'like', "%{$search}%")
                        ->orWhere('apellidos', 'like', "%{$search}%")
                        ->orWhere('documento_identidad', 'like', "%{$search}%");
                });
            })
            ->when($filtros['estado'] ?? null, function ($query, $estado) {
                $query->when($estado === 'Urgente', fn ($q) => $q->whereHas('alertas', fn ($a) => $a
                    ->where('es_leida', false)
                    ->where('nivel_severidad', 'Crítico')))
                    ->when($estado === 'Pendiente', fn ($q) => $q->whereHas('alertas', fn ($a) => $a
                        ->where('es_leida', false)
                        ->where('nivel_severidad', '!=', 'Crítico')))
                    ->when($estado === 'Al día', fn ($q) => $q->whereDoesntHave('alertas', fn ($a) => $a
                        ->where('es_leida', false)));
            })
            ->with('enfermedades')
            ->with([
                'historiasClinicas' => fn ($query) => $query->latest('fecha_atencion')->limit(1)->with('medico'),
            ])
            ->withCount(['alertas as alertas_sin_leer' => fn ($q) => $q->where('es_leida', false)])
            ->withCount(['alertas as alertas_criticas_sin_leer' => fn ($q) => $q
                ->where('es_leida', false)
                ->where('nivel_severidad', 'Crítico')])
            ->orderBy('apellidos')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.seguimiento', [
            'pacientes' => $pacientes,
            'pacientesActivos' => Paciente::activos()->orderBy('apellidos')->get(),
            'estadisticas' => [
                'controles_hoy' => HistoriaClinica::whereDate('fecha_atencion', now()->toDateString())->count(),
                'sin_leer' => Alerta::noLeidas()->count(),
                'criticas' => Alerta::noLeidas()->criticas()->count(),
                'en_seguimiento' => $this->baseQuery()->count(),
            ],
        ]);
    }

    public function show(Paciente $paciente): Paciente
    {
        return $paciente->load([
            'enfermedades',
            'historiasClinicas' => fn ($query) => $query->latest('fecha_atencion')->limit(1)
                ->with(['medico', 'signosVitales']),
            'alertas' => fn ($query) => $query->noLeidas()->latest()->limit(15),
        ]);
    }

    public function store(StoreControlRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $signos = $this->datosSignosVitales($request, $data);
        $paciente = Paciente::findOrFail($data['paciente_id']);
        unset($data['paciente_id']);
        $this->quitarSignosDe($data, $request);

        $historia = HistoriaClinica::create($data + [
            'paciente_id' => $paciente->id,
            'medico_id' => auth()->id(),
        ]);

        $signoVital = $signos === [] ? null : SignoVital::create($signos + [
            'historia_clinica_id' => $historia->id,
            'paciente_id' => $paciente->id,
        ]);

        $alertas = $this->generadorDeAlertas->regenerar($paciente, $historia, $signoVital);

        return redirect()
            ->route('dashboard.seguimiento')
            ->with('success', $this->mensajeControl('registrado', $paciente, $alertas));
    }

    public function update(UpdateControlRequest $request, HistoriaClinica $control): RedirectResponse
    {
        $data = $request->validated();
        $signos = $this->datosSignosVitales($request, $data);
        $this->quitarSignosDe($data, $request);

        // El paciente de la historia es inmutable
        $data['paciente_id'] = $control->paciente_id;
        $control->update($data);

        $signoVital = null;
        if ($signos !== []) {
            $signoVital = $control->signosVitales()->first();
            if ($signoVital) {
                $signoVital->update($signos);
            } else {
                $signoVital = SignoVital::create($signos + [
                    'historia_clinica_id' => $control->id,
                    'paciente_id' => $control->paciente_id,
                ]);
            }
        } else {
            $signoVital = $control->signosVitales()->first();
        }

        $alertas = $this->generadorDeAlertas->regenerar($control->paciente, $control, $signoVital);

        return redirect()
            ->route('dashboard.seguimiento')
            ->with('success', $this->mensajeControl('actualizado', $control->paciente, $alertas));
    }

    public function marcarLeida(Alerta $alerta): RedirectResponse
    {
        $alerta->update([
            'es_leida' => true,
            'fecha_lectura' => now(),
            'usuario_lectura_id' => auth()->id(),
        ]);

        return redirect()
            ->route('dashboard.seguimiento')
            ->with('success', 'Alerta marcada como leída.');
    }

    /**
     * Pacientes en seguimiento: activos con al menos un control o una alerta.
     */
    private function baseQuery()
    {
        return Paciente::query()
            ->where('estado', 'Activo')
            ->where(fn ($q) => $q->whereHas('historiasClinicas')->orWhereHas('alertas'));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function datosSignosVitales(StoreControlRequest $request, array $data): array
    {
        $signos = [];
        foreach ($request->signosVitales() as $campo) {
            if (array_key_exists($campo, $data) && $data[$campo] !== null) {
                $signos[$campo] = $data[$campo];
            }
        }

        return $signos;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function quitarSignosDe(array &$data, StoreControlRequest $request): void
    {
        foreach ($request->signosVitales() as $campo) {
            unset($data[$campo]);
        }
    }

    private function mensajeControl(string $accion, Paciente $paciente, int $alertas): string
    {
        $mensaje = "Control {$accion} para {$paciente->nombre_completo}.";

        if ($alertas > 0) {
            $mensaje .= " Se generaron {$alertas} alerta(s) por valores fuera de rango.";
        }

        return $mensaje;
    }
}
