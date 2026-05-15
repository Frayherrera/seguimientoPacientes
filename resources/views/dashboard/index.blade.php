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
            $statsData = [
                ['title' => 'Total Pacientes', 'value' => '1,234', 'change' => '+12.5%', 'trend' => 'up', 'icon' => 'fa-users', 'color' => 'blue'],
                ['title' => 'Pacientes Activos', 'value' => '856', 'change' => '+8.2%', 'trend' => 'up', 'icon' => 'fa-user-check', 'color' => 'green'],
                ['title' => 'Citas del Día', 'value' => '42', 'change' => '+5.4%', 'trend' => 'up', 'icon' => 'fa-calendar', 'color' => 'purple'],
                ['title' => 'Pacientes Críticos', 'value' => '18', 'change' => '-2.1%', 'trend' => 'down', 'icon' => 'fa-exclamation-triangle', 'color' => 'red'],
            ];
        @endphp

        @foreach($statsData as $stat)
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ $stat['title'] }}</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stat['value'] }}</p>
                        <div class="flex items-center gap-1 mt-2">
                            <i class="fas fa-chart-line w-4 h-4 {{ $stat['trend'] === 'up' ? 'text-green-500' : 'text-red-500' }}"></i>
                            <span class="text-sm font-medium {{ $stat['trend'] === 'up' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $stat['change'] }}
                            </span>
                        </div>
                    </div>
                    <div class="p-3 rounded-lg 
                        {{ $stat['color'] === 'blue' ? 'bg-blue-100' : '' }}
                        {{ $stat['color'] === 'green' ? 'bg-green-100' : '' }}
                        {{ $stat['color'] === 'purple' ? 'bg-purple-100' : '' }}
                        {{ $stat['color'] === 'red' ? 'bg-red-100' : '' }}">
                        <i class="fas {{ $stat['icon'] }} w-6 h-6 
                            {{ $stat['color'] === 'blue' ? 'text-blue-600' : '' }}
                            {{ $stat['color'] === 'green' ? 'text-green-600' : '' }}
                            {{ $stat['color'] === 'purple' ? 'text-purple-600' : '' }}
                            {{ $stat['color'] === 'red' ? 'text-red-600' : '' }} text-xl"></i>
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
            <div class="w-full h-[300px]">
                <canvas id="diseasesBarChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Pie Chart & Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Pie Chart -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Controles Realizados</h3>
            <div class="w-full h-[250px]">
                <canvas id="controlsPieChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actividad Reciente</h3>
            <div class="space-y-4">
                @php
                    $activities = [
                        ['icon' => 'fa-activity', 'color' => 'blue', 'title' => 'Nuevo paciente registrado', 'time' => 'Hace 5 minutos'],
                        ['icon' => 'fa-calendar', 'color' => 'green', 'title' => 'Cita programada para Juan Pérez', 'time' => 'Hace 15 minutos'],
                        ['icon' => 'fa-exclamation-triangle', 'color' => 'red', 'title' => 'Alerta: Paciente Carlos López requiere atención', 'time' => 'Hace 30 minutos'],
                        ['icon' => 'fa-clock', 'color' => 'purple', 'title' => 'Seguimiento completado para María García', 'time' => 'Hace 1 hora'],
                    ];
                @endphp

                @foreach($activities as $activity)
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-gray-100 rounded-lg">
                            <i class="fas {{ $activity['icon'] }} w-5 h-5 text-{{ $activity['color'] }}-600 text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">{{ $activity['title'] }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
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
                        $recentPatients = [
                            ['name' => 'Juan Pérez', 'disease' => 'Diabetes Tipo 2', 'status' => 'Estable', 'statusColor' => 'green', 'lastControl' => '2026-05-10', 'risk' => 'Bajo', 'riskColor' => 'blue'],
                            ['name' => 'María García', 'disease' => 'Hipertensión', 'status' => 'Control', 'statusColor' => 'yellow', 'lastControl' => '2026-05-12', 'risk' => 'Medio', 'riskColor' => 'orange'],
                            ['name' => 'Carlos López', 'disease' => 'Asma Crónica', 'status' => 'Crítico', 'statusColor' => 'red', 'lastControl' => '2026-05-14', 'risk' => 'Alto', 'riskColor' => 'red'],
                            ['name' => 'Ana Martínez', 'disease' => 'Obesidad', 'status' => 'Estable', 'statusColor' => 'green', 'lastControl' => '2026-05-11', 'risk' => 'Bajo', 'riskColor' => 'blue'],
                            ['name' => 'Pedro Rodríguez', 'disease' => 'Artritis', 'status' => 'Control', 'statusColor' => 'yellow', 'lastControl' => '2026-05-13', 'risk' => 'Medio', 'riskColor' => 'orange'],
                        ];
                    @endphp

                    @foreach($recentPatients as $patient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $patient['name'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ $patient['disease'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $patient['statusColor'] === 'green' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $patient['statusColor'] === 'yellow' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $patient['statusColor'] === 'red' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $patient['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $patient['lastControl'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $patient['riskColor'] === 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $patient['riskColor'] === 'orange' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $patient['riskColor'] === 'red' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $patient['risk'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
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
    document.addEventListener('DOMContentLoaded', function() {
        // Datos para los gráficos
        const patientsData = {
            months: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            values: [120, 150, 180, 220, 250, 290]
        };

        const diseasesData = {
            names: ['Diabetes', 'Hipertensión', 'Asma', 'Obesidad', 'Artritis'],
            values: [320, 280, 150, 200, 100]
        };

        const controlsData = {
            names: ['Consultas', 'Seguimiento', 'Urgencias'],
            values: [450, 320, 180],
            colors: ['#3B82F6', '#10B981', '#EF4444']
        };

        // Gráfico de líneas - Pacientes por mes
        const lineCtx = document.getElementById('patientsLineChart')?.getContext('2d');
        if (lineCtx) {
            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: patientsData.months,
                    datasets: [{
                        label: 'Pacientes',
                        data: patientsData.values,
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

        // Gráfico de barras - Enfermedades más comunes
        const barCtx = document.getElementById('diseasesBarChart')?.getContext('2d');
        if (barCtx) {
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: diseasesData.names,
                    datasets: [{
                        label: 'Casos',
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
                                label: function(context) {
                                    return `Casos: ${context.raw}`;
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

        // Gráfico de pastel - Controles realizados
        const pieCtx = document.getElementById('controlsPieChart')?.getContext('2d');
        if (pieCtx) {
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: controlsData.names,
                    datasets: [{
                        data: controlsData.values,
                        backgroundColor: controlsData.colors,
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
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
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