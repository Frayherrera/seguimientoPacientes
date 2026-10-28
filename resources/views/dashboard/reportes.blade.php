@extends('layouts.dashboard')

@section('title', 'Reportes - Clínica Aguachica')

@section('content')
    @php
        $exportParams = array_filter([
            'periodo' => $filtros['periodo'] ?? null,
            'desde' => $filtros['desde'] ?? null,
            'hasta' => $filtros['hasta'] ?? null,
        ]);
    @endphp

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900">Reportes</h1>
                <p class="text-gray-500 mt-1">Análisis y estadísticas del sistema</p>
            </div>
            <a href="{{ route('dashboard.reportes.export', array_merge(['tipo' => 'resumen'], $exportParams)) }}"
                class="flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-download w-5 h-5"></i>
                Exportar resumen CSV
            </a>
        </div>

        <!-- Filtro de período -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form action="{{ route('dashboard.reportes') }}" method="GET"
                class="flex flex-col lg:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label for="periodo" class="block text-sm font-medium text-gray-700 mb-1">Período</label>
                    <select id="periodo" name="periodo"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="mes" @selected(($filtros['periodo'] ?? 'mes') === 'mes')>Este mes</option>
                        <option value="trimestre" @selected(($filtros['periodo'] ?? '') === 'trimestre')>Este trimestre</option>
                        <option value="anio" @selected(($filtros['periodo'] ?? '') === 'anio')>Este año</option>
                        <option value="personalizado" @selected(($filtros['periodo'] ?? '') === 'personalizado')>Personalizado</option>
                    </select>
                </div>
                <div id="customDateRange" class="flex-1 {{ ($filtros['periodo'] ?? 'mes') === 'personalizado' ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rango de fechas</label>
                    <div class="flex gap-3">
                        <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}" required
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}" required
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i> Aplicar
                </button>
            </form>
            <p class="text-xs text-gray-500 mt-3">
                <i class="far fa-calendar-alt mr-1"></i> {{ $etiquetaPeriodo }}
            </p>
        </div>

        @if ($errors->any())
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3">
                <i class="fas fa-exclamation-circle mt-0.5"></i>
                <div>
                    <p class="font-medium">Corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside text-sm mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Tarjetas de reportes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all group hover:-translate-y-1">
                <div class="mb-4 p-3 rounded-lg inline-block bg-blue-100 text-blue-600">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Reporte de Pacientes</h3>
                <p class="text-3xl font-bold text-gray-900">{{ $pacientes['nuevos'] }}</p>
                <p class="text-sm text-gray-600">nuevos en el período</p>
                <p class="text-xs text-gray-500 mt-2">
                    {{ $pacientes['total'] }} en total · {{ $pacientes['activos'] }} activos
                </p>
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                    <span class="text-xs text-gray-500">{{ $etiquetaPeriodo }}</span>
                    <a href="{{ route('dashboard.reportes.export', array_merge(['tipo' => 'pacientes'], $exportParams)) }}"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                        CSV
                        <i class="fas fa-download text-xs"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all group hover:-translate-y-1">
                <div class="mb-4 p-3 rounded-lg inline-block bg-green-100 text-green-600">
                    <i class="fas fa-calendar text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Reporte de Citas</h3>
                <p class="text-3xl font-bold text-gray-900">{{ $citas['total'] }}</p>
                <p class="text-sm text-gray-600">citas en el período</p>
                <p class="text-xs text-gray-500 mt-2">
                    {{ $citas['atendidas'] }} atendidas · {{ $citas['no_presentadas'] }} no presentadas
                </p>
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                    <span class="text-xs text-gray-500">{{ $etiquetaPeriodo }}</span>
                    <a href="{{ route('dashboard.reportes.export', array_merge(['tipo' => 'citas'], $exportParams)) }}"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                        CSV
                        <i class="fas fa-download text-xs"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all group hover:-translate-y-1">
                <div class="mb-4 p-3 rounded-lg inline-block bg-purple-100 text-purple-600">
                    <i class="fas fa-chart-bar text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Reporte de Enfermedades</h3>
                <p class="text-3xl font-bold text-gray-900">{{ $enfermedades['total_diagnosticos_activos'] }}</p>
                <p class="text-sm text-gray-600">diagnósticos activos</p>
                <p class="text-xs text-gray-500 mt-2">
                    {{ $enfermedades['lista']->count() }} enfermedades registradas
                </p>
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                    <span class="text-xs text-gray-500">Prevalencia actual</span>
                    <a href="{{ route('dashboard.reportes.export', array_merge(['tipo' => 'enfermedades'], $exportParams)) }}"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                        CSV
                        <i class="fas fa-download text-xs"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all group hover:-translate-y-1">
                <div class="mb-4 p-3 rounded-lg inline-block bg-orange-100 text-orange-600">
                    <i class="fas fa-stethoscope text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Reporte de Seguimiento</h3>
                <p class="text-3xl font-bold text-gray-900">{{ $seguimiento['controles'] }}</p>
                <p class="text-sm text-gray-600">controles en el período</p>
                <p class="text-xs text-gray-500 mt-2">
                    {{ $seguimiento['alertas'] }} alertas · {{ $seguimiento['alertas_criticas'] }} críticas
                </p>
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                    <span class="text-xs text-gray-500">{{ $etiquetaPeriodo }}</span>
                    <a href="{{ route('dashboard.reportes.export', array_merge(['tipo' => 'seguimiento'], $exportParams)) }}"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                        CSV
                        <i class="fas fa-download text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pacientes registrados por mes -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Pacientes por Mes</h3>
                <p class="text-xs text-gray-500 mb-4">Registros de los últimos 12 meses</p>
                <div class="h-48">
                    @if (collect($pacientesPorMes)->sum('total') === 0)
                        <div class="h-full flex flex-col items-center justify-center text-gray-400">
                            <i class="fas fa-chart-line text-4xl mb-2 block"></i>
                            <p class="text-sm">No hay pacientes registrados en los últimos 12 meses</p>
                        </div>
                    @else
                        <div class="h-full flex items-end gap-1.5">
                            @foreach ($pacientesPorMes as $barra)
                                <div class="flex-1 h-full flex flex-col items-center justify-end gap-1"
                                    title="{{ $barra['etiqueta'] }}: {{ $barra['total'] }} pacientes nuevos">
                                    <span class="text-xs text-gray-600 font-medium">{{ $barra['total'] ?: '' }}</span>
                                    <div class="w-full bg-blue-500 hover:bg-blue-600 rounded-t transition-colors"
                                        style="height: {{ $barra['porcentaje'] }}%"></div>
                                    <span class="text-xs text-gray-500">{{ $barra['etiqueta'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Distribución de citas -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Distribución de Citas</h3>
                <p class="text-xs text-gray-500 mb-4">{{ $etiquetaPeriodo }}</p>
                @if ($citas['total'] === 0)
                    <div class="h-48 flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-chart-pie text-4xl mb-2 block"></i>
                        <p class="text-sm">No hay citas en el período seleccionado</p>
                    </div>
                @else
                    @php
                        $coloresEstado = [
                            'Programada' => 'bg-orange-500',
                            'Confirmada' => 'bg-blue-500',
                            'Atendida' => 'bg-green-500',
                            'Cancelada' => 'bg-red-500',
                            'No Presentado' => 'bg-gray-500',
                        ];
                        $maximoEstado = max($citas['por_estado']->max(), 1);
                    @endphp
                    <div class="space-y-4">
                        @foreach ($citas['por_estado'] as $estado => $total)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-700">{{ $estado }}</span>
                                    <span class="text-gray-500">
                                        {{ $total }} citas ({{ $citas['total'] > 0 ? (int) round($total / $citas['total'] * 100) : 0 }}%)
                                    </span>
                                </div>
                                <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $coloresEstado[$estado] }}"
                                        style="width: {{ $total > 0 ? max(2, (int) round($total / $maximoEstado * 100)) : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Paneles de detalle -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pacientes -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pacientes por Estado y Género</h3>
                @php
                    $estadosPaciente = [
                        'Activo' => ['total' => $pacientes['activos'], 'chip' => 'bg-green-100 text-green-800'],
                        'Inactivo' => ['total' => $pacientes['inactivos'], 'chip' => 'bg-gray-100 text-gray-700'],
                        'Fallecido' => ['total' => $pacientes['fallecidos'], 'chip' => 'bg-red-100 text-red-800'],
                    ];
                @endphp
                <div class="space-y-3">
                    @foreach ($estadosPaciente as $estado => $datos)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $datos['chip'] }}">
                                {{ $estado }}
                            </span>
                            <div class="text-right">
                                <span class="font-semibold text-gray-900">{{ $datos['total'] }}</span>
                                <span class="text-xs text-gray-500 ml-1">
                                    ({{ $pacientes['total'] > 0 ? (int) round($datos['total'] / $pacientes['total'] * 100) : 0 }}%)
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-sm font-medium text-gray-700 mb-2">Pacientes activos por género</p>
                    <div class="flex gap-4 text-sm text-gray-600">
                        <span><span class="font-semibold text-gray-900">{{ $pacientes['por_genero']['M'] ?? 0 }}</span> Masculino</span>
                        <span><span class="font-semibold text-gray-900">{{ $pacientes['por_genero']['F'] ?? 0 }}</span> Femenino</span>
                        <span><span class="font-semibold text-gray-900">{{ $pacientes['por_genero']['Otro'] ?? 0 }}</span> Otro</span>
                    </div>
                </div>
            </div>

            <!-- Citas -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Citas por Tipo y Atención</h3>
                <div class="flex items-baseline gap-2 mb-4">
                    <span class="text-3xl font-bold text-green-600">{{ $citas['tasa_asistencia'] ?? '—' }}{{ $citas['tasa_asistencia'] !== null ? '%' : '' }}</span>
                    <span class="text-sm text-gray-600">tasa de asistencia</span>
                </div>
                <p class="text-xs text-gray-500 mb-4">Atendidas sobre citas no canceladas del período</p>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    @foreach ($citas['por_tipo'] as $tipo => $total)
                        <div class="flex items-center justify-between py-1.5 px-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">{{ $tipo }}</span>
                            <span class="font-semibold text-gray-900">{{ $total }}</span>
                        </div>
                    @endforeach
                </div>
                @if ($citas['por_medico']->isNotEmpty())
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-sm font-medium text-gray-700 mb-2">Médicos con más citas</p>
                        <ol class="space-y-1 text-sm text-gray-600">
                            @foreach ($citas['por_medico'] as $indice => $medico)
                                <li class="flex items-center justify-between">
                                    <span><span class="font-medium text-gray-900">{{ $indice + 1 }}.</span> {{ $medico['nombre'] }}</span>
                                    <span class="text-gray-500">{{ $medico['total'] }} citas</span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif
            </div>

            <!-- Enfermedades -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Prevalencia de Enfermedades</h3>
                @if ($enfermedades['lista']->isEmpty())
                    <div class="py-8 text-center text-gray-400">
                        <i class="fas fa-chart-bar text-4xl mb-2 block"></i>
                        <p class="text-sm">No hay enfermedades registradas</p>
                    </div>
                @else
                    @php
                        $maximoEnfermedad = max($enfermedades['lista']->max('pacientes_total'), 1);
                    @endphp
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th class="pb-2 pr-3">Enfermedad</th>
                                    <th class="pb-2 pr-3">CIE-10</th>
                                    <th class="pb-2 pr-3">Pacientes</th>
                                    <th class="pb-2 text-right">Activos</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($enfermedades['lista'] as $enfermedad)
                                    <tr>
                                        <td class="py-2 pr-3 text-gray-900">{{ $enfermedad->nombre }}</td>
                                        <td class="py-2 pr-3 text-gray-500">{{ $enfermedad->codigo_cie10 ?? '—' }}</td>
                                        <td class="py-2 pr-3">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-gray-900 w-6 text-right">{{ $enfermedad->pacientes_total }}</span>
                                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden min-w-[40px]">
                                                    <div class="h-full bg-purple-500 rounded-full"
                                                        style="width: {{ $enfermedad->pacientes_total > 0 ? max(2, (int) round($enfermedad->pacientes_total / $maximoEnfermedad * 100)) : 0 }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2 text-right text-gray-600">{{ $enfermedad->pacientes_activos }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Seguimiento -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Seguimiento y Alertas</h3>
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="bg-orange-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-orange-600">{{ $seguimiento['controles'] }}</p>
                        <p class="text-xs text-gray-600">Controles</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $seguimiento['pacientes'] }}</p>
                        <p class="text-xs text-gray-600">Pacientes</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-purple-600">{{ $seguimiento['medicos'] }}</p>
                        <p class="text-xs text-gray-600">Médicos</p>
                    </div>
                </div>
                @php
                    $coloresSeveridad = [
                        'Crítico' => 'bg-red-100 text-red-800',
                        'Advertencia' => 'bg-orange-100 text-orange-800',
                        'Info' => 'bg-blue-100 text-blue-800',
                    ];
                @endphp
                <div class="space-y-3">
                    @foreach ($seguimiento['por_severidad'] as $severidad => $total)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $coloresSeveridad[$severidad] }}">
                                {{ $severidad }}
                            </span>
                            <span class="font-semibold text-gray-900">{{ $total }} alertas</span>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-4">
                    {{ $seguimiento['alertas_no_leidas'] }} alertas sin leer de {{ $seguimiento['alertas'] }} en el período
                </p>
            </div>
        </div>
    </div>

    <script>
        // Mostrar/ocultar rango de fechas personalizado
        document.getElementById('periodo')?.addEventListener('change', function () {
            const customRange = document.getElementById('customDateRange');
            if (this.value === 'personalizado') {
                customRange.classList.remove('hidden');
                customRange.querySelectorAll('input').forEach((input) => input.required = true);
            } else {
                customRange.classList.add('hidden');
                customRange.querySelectorAll('input').forEach((input) => input.required = false);
            }
        });
    </script>
@endsection
