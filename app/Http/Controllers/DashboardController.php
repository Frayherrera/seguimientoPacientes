<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Cita;
use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\TipoEnfermedad;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPacientes = Paciente::count();
        $activos = Paciente::activos()->count();
        $citasHoy = Cita::whereDate('fecha_cita', now()->toDateString())->count();
        $citasHoyPendientes = Cita::whereDate('fecha_cita', now()->toDateString())
            ->whereIn('estado', ['Programada', 'Confirmada'])
            ->count();
        $alertasCriticas = Alerta::criticas()->noLeidas();

        return view('dashboard.index', [
            'stats' => [
                [
                    'titulo' => 'Total Pacientes',
                    'valor' => $totalPacientes,
                    'subtitulo' => $this->plural(
                        Paciente::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count(),
                        'nuevo',
                        'este mes'
                    ),
                    'subtitulo_icono' => 'fa-user-plus',
                    'subtitulo_color' => 'text-gray-500',
                    'icono' => 'fa-users',
                    'color' => 'blue',
                ],
                [
                    'titulo' => 'Pacientes Activos',
                    'valor' => $activos,
                    'subtitulo' => ($totalPacientes > 0 ? (int) round($activos / $totalPacientes * 100) : 0).'% del total',
                    'subtitulo_icono' => 'fa-chart-pie',
                    'subtitulo_color' => 'text-gray-500',
                    'icono' => 'fa-user-check',
                    'color' => 'green',
                ],
                [
                    'titulo' => 'Citas del Día',
                    'valor' => $citasHoy,
                    'subtitulo' => $citasHoyPendientes > 0
                        ? $this->plural($citasHoyPendientes, 'pendiente', 'hoy')
                        : 'Sin pendientes hoy',
                    'subtitulo_icono' => 'fa-clock',
                    'subtitulo_color' => $citasHoyPendientes > 0 ? 'text-orange-600' : 'text-gray-500',
                    'icono' => 'fa-calendar',
                    'color' => 'purple',
                ],
                [
                    'titulo' => 'Pacientes Críticos',
                    'valor' => (clone $alertasCriticas)->distinct()->count('paciente_id'),
                    'subtitulo' => (clone $alertasCriticas)->count() > 0
                        ? $this->plural((clone $alertasCriticas)->count(), 'alerta crítica', 'sin leer')
                        : 'Sin alertas críticas sin leer',
                    'subtitulo_icono' => 'fa-exclamation-triangle',
                    'subtitulo_color' => (clone $alertasCriticas)->count() > 0 ? 'text-red-600' : 'text-gray-500',
                    'icono' => 'fa-exclamation-triangle',
                    'color' => 'red',
                ],
            ],
            'pacientesPorMes' => $this->pacientesPorMes(),
            'topEnfermedades' => TipoEnfermedad::where('es_activo', true)
                ->withCount('pacientes')
                ->orderByDesc('pacientes_count')
                ->limit(5)
                ->get(),
            'controlesPorTipo' => HistoriaClinica::select('tipo_atencion', DB::raw('count(*) as total'))
                ->groupBy('tipo_atencion')
                ->orderByDesc('total')
                ->get(),
            'actividad' => $this->actividadReciente(),
            'ultimosPacientes' => $this->ultimosPacientes(),
        ]);
    }

    /**
     * Nuevos pacientes registrados por mes (últimos 12 meses).
     *
     * @return array<int, array{etiqueta: string, total: int}>
     */
    private function pacientesPorMes(): array
    {
        $registrados = Paciente::where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->get(['created_at'])
            ->groupBy(fn ($paciente) => $paciente->created_at->format('Y-m'))
            ->map->count();

        $barras = [];
        for ($i = 11; $i >= 0; $i--) {
            $mes = now()->subMonths($i);
            $barras[] = [
                'etiqueta' => $mes->isoFormat('MMM'),
                'total' => $registrados[$mes->format('Y-m')] ?? 0,
            ];
        }

        return $barras;
    }

    /**
     * Últimos eventos del sistema (pacientes, citas, alertas y controles).
     *
     * @return \Illuminate\Support\Collection<int, array{icono: string, color: string, titulo: string, fecha: \Carbon\Carbon}>
     */
    private function actividadReciente(): \Illuminate\Support\Collection
    {
        $eventos = collect();

        Paciente::latest('created_at')->limit(3)->get()
            ->each(fn (Paciente $paciente) => $eventos->push([
                'icono' => 'fa-user-plus',
                'color' => 'blue',
                'titulo' => "Nuevo paciente registrado: {$paciente->nombre_completo}",
                'fecha' => $paciente->created_at,
            ]));

        Cita::with('paciente')->latest('created_at')->limit(3)->get()
            ->each(fn (Cita $cita) => $eventos->push([
                'icono' => 'fa-calendar-check',
                'color' => 'green',
                'titulo' => sprintf(
                    'Cita %s para %s (%s)',
                    mb_strtolower($cita->estado),
                    $cita->paciente?->nombre_completo,
                    $cita->fecha_cita?->format('d/m/Y')
                ),
                'fecha' => $cita->created_at,
            ]));

        Alerta::with('paciente')->latest('created_at')->limit(3)->get()
            ->each(fn (Alerta $alerta) => $eventos->push([
                'icono' => 'fa-exclamation-triangle',
                'color' => 'red',
                'titulo' => "Alerta {$alerta->nivel_severidad}: {$alerta->paciente?->nombre_completo} — {$alerta->mensaje}",
                'fecha' => $alerta->created_at,
            ]));

        HistoriaClinica::with('paciente')->latest('created_at')->limit(3)->get()
            ->each(fn (HistoriaClinica $historia) => $eventos->push([
                'icono' => 'fa-notes-medical',
                'color' => 'purple',
                'titulo' => "Control registrado para {$historia->paciente?->nombre_completo}",
                'fecha' => $historia->created_at,
            ]));

        return $eventos->sortByDesc('fecha')->take(6)->values();
    }

    /**
     * Últimos pacientes registrados con su enfermedad, último control y
     * nivel de riesgo derivado de sus alertas sin leer.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function ultimosPacientes(): \Illuminate\Support\Collection
    {
        $pacientes = Paciente::with('enfermedades')->latest('created_at')->limit(5)->get();

        if ($pacientes->isEmpty()) {
            return collect();
        }

        $ultimosControles = HistoriaClinica::whereIn('paciente_id', $pacientes->pluck('id'))
            ->groupBy('paciente_id')
            ->selectRaw('paciente_id, max(fecha_atencion) as ultimo_control')
            ->pluck('ultimo_control', 'paciente_id');

        $alertasSinLeer = Alerta::whereIn('paciente_id', $pacientes->pluck('id'))
            ->noLeidas()
            ->get()
            ->groupBy('paciente_id');

        return $pacientes->map(function (Paciente $paciente) use ($ultimosControles, $alertasSinLeer) {
            $severidades = $alertasSinLeer->get($paciente->id)?->pluck('nivel_severidad') ?? collect();

            return [
                'nombre' => $paciente->nombre_completo,
                'enfermedad' => $paciente->enfermedades->first()?->nombre ?? 'Sin enfermedades registradas',
                'estado' => $paciente->estado,
                'ultimo_control' => isset($ultimosControles[$paciente->id])
                    ? \Carbon\Carbon::parse($ultimosControles[$paciente->id])->format('d/m/Y')
                    : 'Sin controles',
                'riesgo' => match (true) {
                    $severidades->contains('Crítico') => 'Alto',
                    $severidades->contains('Advertencia') => 'Medio',
                    default => 'Bajo',
                },
            ];
        });
    }

    private function plural(int $cantidad, string $singular, string $sufijo): string
    {
        return $cantidad.' '.$singular.($cantidad === 1 ? '' : 's').' '.$sufijo;
    }
}
