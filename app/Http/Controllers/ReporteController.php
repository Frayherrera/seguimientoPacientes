<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Cita;
use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\TipoEnfermedad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    private const ESTADOS_CITA = ['Programada', 'Confirmada', 'Atendida', 'Cancelada', 'No Presentado'];

    private const TIPOS_CITA = ['Control', 'Urgencia', 'Primera vez', 'Teleconsulta'];

    private const GENEROS = ['M', 'F', 'Otro'];

    public function index(Request $request): View
    {
        $filtros = $this->validarFiltros($request);
        $rango = $this->rango($filtros);

        return view('dashboard.reportes', [
            'filtros' => $filtros,
            'etiquetaPeriodo' => sprintf(
                'Del %s al %s',
                $rango['desde']->format('d/m/Y'),
                $rango['hasta']->format('d/m/Y')
            ),
            'pacientes' => $this->reportePacientes($rango),
            'citas' => $this->reporteCitas($rango),
            'enfermedades' => $this->reporteEnfermedades(),
            'seguimiento' => $this->reporteSeguimiento($rango),
            'pacientesPorMes' => $this->pacientesPorMes(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filtros = $this->validarFiltros($request);
        $rango = $this->rango($filtros);
        $tipo = $request->validate([
            'tipo' => ['nullable', Rule::in(['resumen', 'pacientes', 'citas', 'enfermedades', 'seguimiento'])],
        ])['tipo'] ?? 'resumen';

        $nombre = 'reporte-'.$tipo.'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($tipo, $rango) {
            $salida = fopen('php://output', 'w');
            // BOM UTF-8 para que Excel reconozca los acentos
            fwrite($salida, "\xEF\xBB\xBF");

            match ($tipo) {
                'pacientes' => $this->csvPacientes($salida, $rango),
                'citas' => $this->csvCitas($salida, $rango),
                'enfermedades' => $this->csvEnfermedades($salida),
                'seguimiento' => $this->csvSeguimiento($salida, $rango),
                default => $this->csvResumen($salida, $rango),
            };

            fclose($salida);
        }, $nombre, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @param  array{desde: Carbon, hasta: Carbon}  $rango
     * @return array<string, mixed>
     */
    private function reportePacientes(array $rango): array
    {
        $porEstado = Paciente::query()
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $porGenero = Paciente::activos()
            ->select('genero', DB::raw('count(*) as total'))
            ->groupBy('genero')
            ->pluck('total', 'genero');

        return [
            'total' => Paciente::count(),
            'activos' => $porEstado['Activo'] ?? 0,
            'inactivos' => $porEstado['Inactivo'] ?? 0,
            'fallecidos' => $porEstado['Fallecido'] ?? 0,
            'nuevos' => Paciente::whereBetween('created_at', [$rango['desde'], $rango['hasta']])->count(),
            'por_genero' => collect(self::GENEROS)
                ->mapWithKeys(fn ($genero) => [$genero => $porGenero[$genero] ?? 0]),
        ];
    }

    /**
     * @param  array{desde: Carbon, hasta: Carbon}  $rango
     * @return array<string, mixed>
     */
    private function reporteCitas(array $rango): array
    {
        $base = Cita::whereDate('fecha_cita', '>=', $rango['desde']->toDateString())
            ->whereDate('fecha_cita', '<=', $rango['hasta']->toDateString());

        $porEstado = collect(self::ESTADOS_CITA)
            ->mapWithKeys(fn ($estado) => [
                $estado => (clone $base)->where('estado', $estado)->count(),
            ]);

        $porTipo = collect(self::TIPOS_CITA)
            ->mapWithKeys(fn ($tipo) => [
                $tipo => (clone $base)->where('tipo_cita', $tipo)->count(),
            ]);

        $total = (clone $base)->count();
        $canceladas = $porEstado['Cancelada'] ?? 0;
        $atendidas = $porEstado['Atendida'] ?? 0;
        $evaluables = $total - $canceladas;

        return [
            'total' => $total,
            'por_estado' => $porEstado,
            'por_tipo' => $porTipo,
            'atendidas' => $atendidas,
            'no_presentadas' => $porEstado['No Presentado'] ?? 0,
            'canceladas' => $canceladas,
            'tasa_asistencia' => $evaluables > 0 ? (int) round($atendidas / $evaluables * 100) : null,
            'por_medico' => (clone $base)
                ->join('users', 'users.id', '=', 'citas.medico_id')
                ->select('users.nombre', 'users.apellidos', DB::raw('count(*) as total'))
                ->groupBy('users.id', 'users.nombre', 'users.apellidos')
                ->orderByDesc('total')
                ->limit(3)
                ->get()
                ->map(fn ($fila) => [
                    'nombre' => "{$fila->nombre} {$fila->apellidos}",
                    'total' => (int) $fila->total,
                ]),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function reporteEnfermedades(): array
    {
        $enfermedades = TipoEnfermedad::where('es_activo', true)
            ->withCount(['pacientes as pacientes_total'])
            ->withCount(['pacientes as pacientes_activos' => fn ($query) => $query->where('pacientes.estado', 'Activo')])
            ->orderByDesc('pacientes_total')
            ->get();

        return [
            'total_diagnosticos_activos' => $enfermedades->sum('pacientes_activos'),
            'lista' => $enfermedades,
        ];
    }

    /**
     * @param  array{desde: Carbon, hasta: Carbon}  $rango
     * @return array<string, mixed>
     */
    private function reporteSeguimiento(array $rango): array
    {
        $controles = HistoriaClinica::whereDate('fecha_atencion', '>=', $rango['desde']->toDateString())
            ->whereDate('fecha_atencion', '<=', $rango['hasta']->toDateString());

        $alertas = Alerta::whereBetween('created_at', [$rango['desde'], $rango['hasta']]);

        $porSeveridad = collect(['Crítico', 'Advertencia', 'Info'])
            ->mapWithKeys(fn ($severidad) => [
                $severidad => (clone $alertas)->where('nivel_severidad', $severidad)->count(),
            ]);

        return [
            'controles' => (clone $controles)->count(),
            'pacientes' => (clone $controles)->distinct()->count('paciente_id'),
            'medicos' => (clone $controles)->distinct()->count('medico_id'),
            'alertas' => (clone $alertas)->count(),
            'alertas_criticas' => $porSeveridad['Crítico'] ?? 0,
            'por_severidad' => $porSeveridad,
            'alertas_no_leidas' => (clone $alertas)->noLeidas()->count(),
        ];
    }

    /**
     * Altura de las barras del gráfico "pacientes por mes" (últimos 12 meses).
     *
     * @return array<int, array{etiqueta: string, total: int, porcentaje: int}>
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
            $total = $registrados[$mes->format('Y-m')] ?? 0;
            $barras[] = [
                'etiqueta' => $mes->isoFormat('MMM'),
                'total' => $total,
                'porcentaje' => 0,
            ];
        }

        $maximo = max(collect($barras)->max('total'), 1);
        foreach ($barras as &$barra) {
            if ($barra['total'] > 0) {
                // Mínimo 4% de altura para que se vean barras pequeñas
                $barra['porcentaje'] = max(4, (int) round($barra['total'] / $maximo * 100));
            }
        }

        return $barras;
    }

    /**
     * @return array{periodo: string, desde: ?string, hasta: ?string}
     */
    private function validarFiltros(Request $request): array
    {
        return $request->validate([
            'periodo' => ['nullable', 'string', Rule::in(['mes', 'trimestre', 'anio', 'personalizado'])],
            'desde' => ['date', 'required_if:periodo,personalizado'],
            'hasta' => ['date', 'after_or_equal:desde', 'required_if:periodo,personalizado'],
        ]);
    }

    /**
     * @param  array{periodo: ?string, desde: ?string, hasta: ?string}  $filtros
     * @return array{desde: Carbon, hasta: Carbon}
     */
    private function rango(array $filtros): array
    {
        return match ($filtros['periodo'] ?? 'mes') {
            'trimestre' => ['desde' => now()->startOfQuarter()->startOfDay(), 'hasta' => now()->endOfDay()],
            'anio' => ['desde' => now()->startOfYear()->startOfDay(), 'hasta' => now()->endOfDay()],
            'personalizado' => [
                'desde' => Carbon::parse($filtros['desde'])->startOfDay(),
                'hasta' => Carbon::parse($filtros['hasta'])->endOfDay(),
            ],
            default => ['desde' => now()->startOfMonth()->startOfDay(), 'hasta' => now()->endOfDay()],
        };
    }

    /**
     * @param  resource  $salida
     * @param  array{desde: Carbon, hasta: Carbon}  $rango
     */
    private function csvResumen($salida, array $rango): void
    {
        fputcsv($salida, ['Reporte general', $this->etiquetaRango($rango)], ';');
        fputcsv($salida, [], ';');

        $pacientes = $this->reportePacientes($rango);
        fputcsv($salida, ['Pacientes'], ';');
        fputcsv($salida, ['Total', $pacientes['total']], ';');
        fputcsv($salida, ['Activos', $pacientes['activos']], ';');
        fputcsv($salida, ['Inactivos', $pacientes['inactivos']], ';');
        fputcsv($salida, ['Fallecidos', $pacientes['fallecidos']], ';');
        fputcsv($salida, ['Nuevos en el período', $pacientes['nuevos']], ';');
        fputcsv($salida, [], ';');

        $citas = $this->reporteCitas($rango);
        fputcsv($salida, ['Citas'], ';');
        fputcsv($salida, ['Total en el período', $citas['total']], ';');
        foreach ($citas['por_estado'] as $estado => $total) {
            fputcsv($salida, [$estado, $total], ';');
        }
        fputcsv($salida, ['Tasa de asistencia (%)', $citas['tasa_asistencia'] ?? '—'], ';');
        fputcsv($salida, [], ';');

        fputcsv($salida, ['Enfermedades'], ';');
        fputcsv($salida, ['Enfermedad', 'CIE-10', 'Pacientes', 'Pacientes activos'], ';');
        foreach ($this->reporteEnfermedades()['lista'] as $enfermedad) {
            fputcsv($salida, [
                $enfermedad->nombre,
                $enfermedad->codigo_cie10,
                $enfermedad->pacientes_total,
                $enfermedad->pacientes_activos,
            ], ';');
        }
        fputcsv($salida, [], ';');

        $seguimiento = $this->reporteSeguimiento($rango);
        fputcsv($salida, ['Seguimiento'], ';');
        fputcsv($salida, ['Controles en el período', $seguimiento['controles']], ';');
        fputcsv($salida, ['Pacientes en seguimiento', $seguimiento['pacientes']], ';');
        fputcsv($salida, ['Alertas generadas', $seguimiento['alertas']], ';');
        foreach ($seguimiento['por_severidad'] as $severidad => $total) {
            fputcsv($salida, ["Alertas {$severidad}", $total], ';');
        }
    }

    /**
     * @param  resource  $salida
     * @param  array{desde: Carbon, hasta: Carbon}  $rango
     */
    private function csvPacientes($salida, array $rango): void
    {
        fputcsv($salida, [
            'Documento', 'Nombres', 'Apellidos', 'Género', 'Edad', 'EPS',
            'Estado', 'Enfermedades', 'Fecha de registro',
        ], ';');

        Paciente::whereBetween('created_at', [$rango['desde'], $rango['hasta']])
            ->withCount('enfermedades')
            ->orderBy('created_at')
            ->get()
            ->each(function ($paciente) use ($salida) {
                fputcsv($salida, [
                    $paciente->documento_identidad,
                    $paciente->nombres,
                    $paciente->apellidos,
                    $paciente->genero,
                    $paciente->edad ?? '—',
                    $paciente->eps,
                    $paciente->estado,
                    $paciente->enfermedades_count,
                    $paciente->created_at->format('d/m/Y'),
                ], ';');
            });
    }

    /**
     * @param  resource  $salida
     * @param  array{desde: Carbon, hasta: Carbon}  $rango
     */
    private function csvCitas($salida, array $rango): void
    {
        fputcsv($salida, [
            'Fecha', 'Hora', 'Paciente', 'Documento', 'Médico', 'Tipo', 'Motivo', 'Estado',
        ], ';');

        Cita::whereDate('fecha_cita', '>=', $rango['desde']->toDateString())
            ->whereDate('fecha_cita', '<=', $rango['hasta']->toDateString())
            ->with(['paciente', 'medico'])
            ->orderBy('fecha_cita')
            ->orderBy('hora_inicio')
            ->get()
            ->each(function ($cita) use ($salida) {
                fputcsv($salida, [
                    $cita->fecha_cita?->format('d/m/Y'),
                    substr((string) $cita->hora_inicio, 0, 5),
                    $cita->paciente?->nombre_completo,
                    $cita->paciente?->documento_identidad,
                    $cita->medico?->nombre_completo,
                    $cita->tipo_cita,
                    $cita->motivo,
                    $cita->estado,
                ], ';');
            });
    }

    /**
     * @param  resource  $salida
     */
    private function csvEnfermedades($salida): void
    {
        fputcsv($salida, ['Enfermedad', 'CIE-10', 'Pacientes', 'Pacientes activos'], ';');

        $this->reporteEnfermedades()['lista']->each(function ($enfermedad) use ($salida) {
            fputcsv($salida, [
                $enfermedad->nombre,
                $enfermedad->codigo_cie10,
                $enfermedad->pacientes_total,
                $enfermedad->pacientes_activos,
            ], ';');
        });
    }

    /**
     * @param  resource  $salida
     * @param  array{desde: Carbon, hasta: Carbon}  $rango
     */
    private function csvSeguimiento($salida, array $rango): void
    {
        fputcsv($salida, [
            'Fecha', 'Hora', 'Paciente', 'Impresión Diagnóstica', 'Médico', 'Alertas',
        ], ';');

        HistoriaClinica::whereDate('fecha_atencion', '>=', $rango['desde']->toDateString())
            ->whereDate('fecha_atencion', '<=', $rango['hasta']->toDateString())
            ->with(['paciente', 'medico'])
            ->withCount('alertas')
            ->orderByDesc('fecha_atencion')
            ->orderByDesc('hora_atencion')
            ->get()
            ->each(function ($historia) use ($salida) {
                fputcsv($salida, [
                    $historia->fecha_atencion?->format('d/m/Y'),
                    substr((string) $historia->hora_atencion, 0, 5),
                    $historia->paciente?->nombre_completo,
                    $historia->impresion_diagnostica,
                    $historia->medico?->nombre_completo,
                    $historia->alertas_count,
                ], ';');
            });
    }

    /**
     * @param  array{desde: Carbon, hasta: Carbon}  $rango
     */
    private function etiquetaRango(array $rango): string
    {
        return sprintf('Del %s al %s', $rango['desde']->format('d/m/Y'), $rango['hasta']->format('d/m/Y'));
    }
}
