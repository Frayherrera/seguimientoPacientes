@extends('layouts.dashboard')

@section('title', 'Dashboard - Clínica Aguachica')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Dashboard</h1>
            <p class="text-gray-500 mt-1">Resumen general del sistema de gestión de pacientes</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $coloresCard = [
                    'blue' => ['fondo' => 'bg-blue-100', 'icono' => 'text-blue-600'],
                    'green' => ['fondo' => 'bg-green-100', 'icono' => 'text-green-600'],
                    'purple' => ['fondo' => 'bg-purple-100', 'icono' => 'text-purple-600'],
                    'red' => ['fondo' => 'bg-red-100', 'icono' => 'text-red-600'],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">{{ $stat['titulo'] }}</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stat['valor'] }}</p>
                            <div class="flex items-center gap-1 mt-2">
                                <i class="fas {{ $stat['subtitulo_icono'] }} w-4 h-4 {{ $stat['subtitulo_color'] }}"></i>
                                <span class="text-sm {{ $stat['subtitulo_color'] }}">{{ $stat['subtitulo'] }}</span>
                            </div>
                        </div>
                        <div class="p-3 rounded-lg {{ $coloresCard[$stat['color']]['fondo'] }}">
                            <i class="fas {{ $stat['icono'] }} w-6 h-6 {{ $coloresCard[$stat['color']]['icono'] }} text-xl"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Line Chart - Pacientes por Mes -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pacientes por Mes</h3>
                <div class="w-full h-[300px]">
                    <canvas id="patientsLineChart" class="w-full h-full"></canvas>
                </div>
            </div>

            <!-- Bar Chart - Enfermedades Más Comunes -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Enfermedades Más Comunes</h3>
                @if ($topEnfermedades->isEmpty())
                    <div class="w-full h-[300px] flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-chart-bar text-4xl mb-2 block"></i>
                        <p class="text-sm">No hay enfermedades registradas</p>
                    </div>
                @else
                    <div class="w-full h-[300px]">
                        <canvas id="diseasesBarChart" class="w-full h-full"></canvas>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pie Chart & Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Pie Chart -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Controles Realizados</h3>
                @if ($controlesPorTipo->isEmpty())
                    <div class="w-full h-[250px] flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-chart-pie text-4xl mb-2 block"></i>
                        <p class="text-sm">Sin controles registrados</p>
                    </div>
                @else
                    <div class="w-full h-[250px]">
                        <canvas id="controlsPieChart" class="w-full h-full"></canvas>
                    </div>
                @endif
            </div>

            <!-- Recent Activity -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actividad Reciente</h3>
                @if ($actividad->isEmpty())
                    <div class="py-8 text-center text-gray-400">
                        <i class="fas fa-activity text-4xl mb-2 block"></i>
                        <p class="text-sm">Todavía no hay actividad registrada</p>
                    </div>
                @else
                    @php
                        $coloresActividad = [
                            'blue' => 'text-blue-600',
                            'green' => 'text-green-600',
                            'red' => 'text-red-600',
                            'purple' => 'text-purple-600',
                        ];
                    @endphp
                    <div class="space-y-4">
                        @foreach ($actividad as $evento)
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-gray-100 rounded-lg">
                                    <i class="fas {{ $evento['icono'] }} w-5 h-5 {{ $coloresActividad[$evento['color']] }} text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900">{{ $evento['titulo'] }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $evento['fecha']->locale('es')->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Patients Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Últimos Registros</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enfermedad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Último Control</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel de Riesgo</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $estadosPaciente = [
                                'Activo' => 'bg-green-100 text-green-800',
                                'Inactivo' => 'bg-gray-100 text-gray-700',
                                'Fallecido' => 'bg-red-100 text-red-800',
                            ];
                            $riesgos = [
                                'Bajo' => 'bg-blue-100 text-blue-800',
                                'Medio' => 'bg-orange-100 text-orange-800',
                                'Alto' => 'bg-red-100 text-red-800',
                            ];
                        @endphp

                        @forelse($ultimosPacientes as $paciente)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $paciente['nombre'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">{{ $paciente['enfermedad'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $estadosPaciente[$paciente['estado']] }}">
                                        {{ $paciente['estado'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $paciente['ultimo_control'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $riesgos[$paciente['riesgo']] }}">
                                        {{ $paciente['riesgo'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-users text-4xl text-gray-300 mb-3 block"></i>
                                    <p>No hay pacientes registrados todavía</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Datos reales del servidor
            const patientsData = @json($pacientesPorMes);
            const diseasesData = {
                names: @json($topEnfermedades->pluck('nombre')->values()),
                values: @json($topEnfermedades->pluck('pacientes_count')->values())
            };
            const controlsData = {
                names: @json($controlesPorTipo->pluck('tipo_atencion')->values()),
                values: @json($controlesPorTipo->pluck('total')->values()),
                colors: ['#3B82F6', '#10B981', '#EF4444', '#F59E0B', '#8B5CF6']
            };

            // Gráfico de líneas - Pacientes por mes (últimos 12 meses)
            const lineCtx = document.getElementById('patientsLineChart')?.getContext('2d');
            if (lineCtx) {
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: patientsData.map((mes) => mes.etiqueta),
                        datasets: [{
                            label: 'Pacientes',
                            data: patientsData.map((mes) => mes.total),
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#3B82F6',
                            pointBorderColor: '#fff',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#E5E7EB'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Gráfico de barras - Enfermedades más comunes (por prevalencia)
            const barCtx = document.getElementById('diseasesBarChart')?.getContext('2d');
            if (barCtx) {
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: diseasesData.names,
                        datasets: [{
                            label: 'Pacientes',
                            data: diseasesData.values,
                            backgroundColor: '#10B981',
                            borderRadius: 8,
                            barPercentage: 0.7,
                            categoryPercentage: 0.8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return `Pacientes: ${context.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#E5E7EB'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Gráfico de pastel - Controles realizados por tipo de atención
            const pieCtx = document.getElementById('controlsPieChart')?.getContext('2d');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: controlsData.names,
                        datasets: [{
                            data: controlsData.values,
                            backgroundColor: controlsData.values.map((_, i) => controlsData.colors[i % controlsData.colors.length]),
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 15
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
