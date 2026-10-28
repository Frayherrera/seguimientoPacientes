<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HistorialController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $this->validarFiltros($request);

        $query = $this->filtrar(HistoriaClinica::query(), $filtros);

        $registros = $query
            ->with(['paciente', 'medico', 'prescripciones.medicamento'])
            ->orderByDesc('fecha_atencion')
            ->orderByDesc('hora_atencion')
            ->paginate(10)
            ->withQueryString();

        $base = $this->filtrar(HistoriaClinica::query(), $filtros);

        return view('dashboard.historial', [
            'registros' => $registros,
            'medicos' => User::whereIn('id', HistoriaClinica::query()->distinct()->pluck('medico_id'))
                ->orderBy('apellidos')
                ->get(),
            'filtros' => $filtros,
            'estadisticas' => [
                'total' => (clone $base)->count(),
                'este_mes' => (clone $base)->whereMonth('fecha_atencion', now()->month)
                    ->whereYear('fecha_atencion', now()->year)
                    ->count(),
                'pacientes' => (clone $base)->distinct()->count('paciente_id'),
                'medicos' => (clone $base)->distinct()->count('medico_id'),
                'hoy' => (clone $base)->whereDate('fecha_atencion', now()->toDateString())->count(),
                'ayer' => (clone $base)->whereDate('fecha_atencion', now()->subDay()->toDateString())->count(),
            ],
        ]);
    }

    public function show(HistoriaClinica $historial): HistoriaClinica
    {
        $historial->load([
            'paciente',
            'medico',
            'signosVitales',
            'prescripciones.medicamento',
        ]);

        // Eloquent serializa las relaciones en snake_case: el JSON expone "proxima_cita"
        $historial->setRelation(
            'proxima_cita',
            $historial->paciente->citas()
                ->where('fecha_cita', '>=', now()->toDateString())
                ->whereIn('estado', ['Programada', 'Confirmada'])
                ->orderBy('fecha_cita')
                ->first()
        );

        return $historial;
    }

    public function export(Request $request): StreamedResponse
    {
        $filtros = $this->validarFiltros($request);

        $registros = $this->filtrar(HistoriaClinica::query(), $filtros)
            ->with(['paciente', 'medico'])
            ->orderByDesc('fecha_atencion')
            ->orderByDesc('hora_atencion')
            ->get();

        $nombre = 'historial-clinico-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($registros) {
            $salida = fopen('php://output', 'w');
            // BOM UTF-8 para que Excel reconozca los acentos
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, [
                'Fecha', 'Hora', 'Paciente', 'Documento', 'Tipo de Atención',
                'Motivo', 'Impresión Diagnóstica', 'Médico', 'Estado',
            ], ';');

            foreach ($registros as $registro) {
                fputcsv($salida, [
                    $registro->fecha_atencion?->format('d/m/Y'),
                    substr((string) $registro->hora_atencion, 0, 5),
                    $registro->paciente?->nombre_completo,
                    $registro->paciente?->documento_identidad,
                    $registro->tipo_atencion,
                    $registro->motivo_consulta,
                    $registro->impresion_diagnostica,
                    $registro->medico?->nombre_completo,
                    $registro->estado,
                ], ';');
            }

            fclose($salida);
        }, $nombre, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array{search: ?string, periodo: ?string, medico_id: ?int}
     */
    private function validarFiltros(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'periodo' => ['nullable', 'string', 'in:hoy,semana,mes,anio'],
            'medico_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);
    }

    /**
     * @param  array{search: ?string, periodo: ?string, medico_id: ?int}  $filtros
     */
    private function filtrar($query, array $filtros)
    {
        return $query
            ->when($filtros['search'] ?? null, function ($query, $search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('impresion_diagnostica', 'like', "%{$search}%")
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
            ->when($filtros['periodo'] ?? null, function ($query, $periodo) {
                match ($periodo) {
                    'hoy' => $query->whereDate('fecha_atencion', now()->toDateString()),
                    'semana' => $query->whereDate('fecha_atencion', '>=', now()->startOfWeek()->toDateString()),
                    'mes' => $query->whereDate('fecha_atencion', '>=', now()->startOfMonth()->toDateString()),
                    'anio' => $query->whereDate('fecha_atencion', '>=', now()->startOfYear()->toDateString()),
                };
            })
            ->when($filtros['medico_id'] ?? null, fn ($query, $medicoId) => $query->where('medico_id', $medicoId));
    }
}
