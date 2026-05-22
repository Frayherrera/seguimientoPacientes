@extends('layouts.dashboard')

@section('title', 'Reportes - Clínica Aguachica')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Reportes</h1>
            <p class="text-gray-500 mt-1">Análisis y estadísticas del sistema</p>
        </div>
        <div class="flex gap-3">
            <button id="exportAllBtn" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-download w-5 h-5"></i>
                Exportar Todo
            </button>
            <button id="scheduleReportBtn" class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-clock w-5 h-5"></i>
                Programar Reporte
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex flex-col lg:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Período</label>
                <select id="periodSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="month">Este mes</option>
                    <option value="quarter">Este trimestre</option>
                    <option value="year">Este año</option>
                    <option value="custom">Personalizado</option>
                </select>
            </div>
            <div id="customDateRange" class="flex-1 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Rango de fechas</label>
                <div class="flex gap-3">
                    <input type="date" id="startDate" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="date" id="endDate" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <button id="applyFilterBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-filter mr-2"></i> Aplicar
            </button>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $reportes = [
                ['title' => 'Reporte de Pacientes', 'description' => 'Estadísticas generales de pacientes', 'icon' => 'fa-chart-line', 'color' => 'blue', 'date' => date('F Y'), 'type' => 'patients'],
                ['title' => 'Reporte de Citas', 'description' => 'Análisis de citas programadas', 'icon' => 'fa-calendar', 'color' => 'green', 'date' => date('F Y'), 'type' => 'appointments'],
                ['title' => 'Reporte de Enfermedades', 'description' => 'Prevalencia de enfermedades', 'icon' => 'fa-chart-bar', 'color' => 'purple', 'date' => date('F Y'), 'type' => 'diseases'],
                ['title' => 'Reporte de Seguimiento', 'description' => 'Control de pacientes crónicos', 'icon' => 'fa-stethoscope', 'color' => 'orange', 'date' => date('F Y'), 'type' => 'tracking'],
                ['title' => 'Reporte Financiero', 'description' => 'Ingresos y facturación', 'icon' => 'fa-chart-pie', 'color' => 'red', 'date' => date('F Y'), 'type' => 'financial'],
                ['title' => 'Reporte de Calidad', 'description' => 'Indicadores de satisfacción', 'icon' => 'fa-star', 'color' => 'yellow', 'date' => date('F Y'), 'type' => 'quality'],
            ];
        @endphp

        @foreach($reportes as $reporte)
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all group hover:-translate-y-1">
                <div class="mb-4 p-3 rounded-lg inline-block
                    {{ $reporte['color'] === 'blue' ? 'bg-blue-100 text-blue-600' : '' }}
                    {{ $reporte['color'] === 'green' ? 'bg-green-100 text-green-600' : '' }}
                    {{ $reporte['color'] === 'purple' ? 'bg-purple-100 text-purple-600' : '' }}
                    {{ $reporte['color'] === 'orange' ? 'bg-orange-100 text-orange-600' : '' }}
                    {{ $reporte['color'] === 'red' ? 'bg-red-100 text-red-600' : '' }}
                    {{ $reporte['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-600' : '' }}">
                    <i class="fas {{ $reporte['icon'] }} text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $reporte['title'] }}</h3>
                <p class="text-sm text-gray-600 mb-4">{{ $reporte['description'] }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">{{ $reporte['date'] }}</span>
                    <button onclick="generateReport('{{ $reporte['type'] }}')" 
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 group">
                        Generar
                        <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Quick Stats Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfico de pacientes por mes (vista previa) -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Pacientes por Mes</h3>
                <button onclick="generateReport('patients')" class="text-xs text-blue-600 hover:text-blue-700">
                    Ver completo →
                </button>
            </div>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center text-gray-400">
                    <i class="fas fa-chart-line text-4xl mb-2 block"></i>
                    <p class="text-sm">Vista previa del gráfico</p>
                    <p class="text-xs">Genere el reporte para ver datos detallados</p>
                </div>
            </div>
        </div>

        <!-- Gráfico de distribución de citas -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Distribución de Citas</h3>
                <button onclick="generateReport('appointments')" class="text-xs text-blue-600 hover:text-blue-700">
                    Ver completo →
                </button>
            </div>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center text-gray-400">
                    <i class="fas fa-chart-pie text-4xl mb-2 block"></i>
                    <p class="text-sm">Vista previa del gráfico</p>
                    <p class="text-xs">Genere el reporte para ver datos detallados</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Reportes Recientes</h3>
                <button id="clearHistoryBtn" class="text-sm text-red-600 hover:text-red-700">
                    Limpiar historial
                </button>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            @php
                $reportesRecientes = [
                    ['name' => 'Reporte Mensual de Pacientes - Abril 2026', 'date' => '2026-05-01', 'size' => '2.4 MB', 'type' => 'patients', 'status' => 'completado'],
                    ['name' => 'Análisis de Citas - Marzo 2026', 'date' => '2026-04-15', 'size' => '1.8 MB', 'type' => 'appointments', 'status' => 'completado'],
                    ['name' => 'Estadísticas Generales - Q1 2026', 'date' => '2026-04-01', 'size' => '3.2 MB', 'type' => 'general', 'status' => 'completado'],
                    ['name' => 'Reporte de Enfermedades - Febrero 2026', 'date' => '2026-03-01', 'size' => '1.5 MB', 'type' => 'diseases', 'status' => 'completado'],
                    ['name' => 'Seguimiento de Pacientes - Enero 2026', 'date' => '2026-02-01', 'size' => '2.1 MB', 'type' => 'tracking', 'status' => 'completado'],
                ];
            @endphp

            @foreach($reportesRecientes as $reporte)
                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg
                            {{ $reporte['type'] === 'patients' ? 'bg-blue-100' : '' }}
                            {{ $reporte['type'] === 'appointments' ? 'bg-green-100' : '' }}
                            {{ $reporte['type'] === 'diseases' ? 'bg-purple-100' : '' }}
                            {{ $reporte['type'] === 'tracking' ? 'bg-orange-100' : '' }}
                            {{ $reporte['type'] === 'general' ? 'bg-gray-100' : '' }}">
                            <i class="fas 
                                {{ $reporte['type'] === 'patients' ? 'fa-chart-line' : '' }}
                                {{ $reporte['type'] === 'appointments' ? 'fa-calendar' : '' }}
                                {{ $reporte['type'] === 'diseases' ? 'fa-chart-bar' : '' }}
                                {{ $reporte['type'] === 'tracking' ? 'fa-stethoscope' : '' }}
                                {{ $reporte['type'] === 'general' ? 'fa-file-alt' : '' }} 
                                text-gray-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $reporte['name'] }}</p>
                            <p class="text-xs text-gray-500">
                                <i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($reporte['date'])->format('d/m/Y') }}
                                <span class="mx-1">•</span>
                                <i class="far fa-file mr-1"></i> {{ $reporte['size'] }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="downloadReport('{{ $reporte['name'] }}')" 
                                class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                title="Descargar">
                            <i class="fas fa-download text-gray-500 group-hover:text-blue-600"></i>
                        </button>
                        <button onclick="shareReport('{{ $reporte['name'] }}')" 
                                class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                title="Compartir">
                            <i class="fas fa-share-alt text-gray-500 group-hover:text-green-600"></i>
                        </button>
                        <button onclick="deleteReport(this)" 
                                class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                title="Eliminar">
                            <i class="fas fa-trash-alt text-gray-500 group-hover:text-red-600"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Ver más -->
        <div class="p-4 border-t border-gray-200 text-center">
            <button id="viewAllReports" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                Ver todos los reportes
                <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>
    </div>
</div>

<!-- Modal para Programar Reporte -->
<div id="scheduleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 transform transition-all">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Programar Reporte</h3>
            <button onclick="closeScheduleModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times w-6 h-6 text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Reporte</label>
                <select id="scheduleReportType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="patients">Reporte de Pacientes</option>
                    <option value="appointments">Reporte de Citas</option>
                    <option value="diseases">Reporte de Enfermedades</option>
                    <option value="tracking">Reporte de Seguimiento</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Frecuencia</label>
                <select id="scheduleFrequency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="daily">Diario</option>
                    <option value="weekly">Semanal</option>
                    <option value="monthly">Mensual</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Formato</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="format" value="pdf" checked class="mr-2"> PDF
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="format" value="excel" class="mr-2"> Excel
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="format" value="csv" class="mr-2"> CSV
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo de envío</label>
                <input type="email" id="scheduleEmail" placeholder="correo@ejemplo.com"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div class="flex gap-3 p-6 pt-0">
            <button onclick="closeScheduleModal()"
                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancelar
            </button>
            <button onclick="saveSchedule()"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-save mr-2"></i> Programar
            </button>
        </div>
    </div>
</div>

<script>
    // Mostrar/ocultar rango de fechas personalizado
    document.getElementById('periodSelect')?.addEventListener('change', function() {
        const customRange = document.getElementById('customDateRange');
        if (this.value === 'custom') {
            customRange.classList.remove('hidden');
        } else {
            customRange.classList.add('hidden');
        }
    });
    
    function generateReport(type) {
        alert(`Generando reporte de tipo: ${type}`);
        // Aquí implementarías la generación del reporte
        // window.location.href = `/dashboard/reportes/generar/${type}`;
    }
    
    function downloadReport(name) {
        alert(`Descargando reporte: ${name}`);
        // Aquí implementarías la descarga del archivo
    }
    
    function shareReport(name) {
        alert(`Compartir reporte: ${name}`);
        // Aquí implementarías el compartir
    }
    
    function deleteReport(button) {
        if (confirm('¿Estás seguro de que deseas eliminar este reporte?')) {
            const row = button.closest('.group');
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 300);
        }
    }
    
    // Exportar todo
    document.getElementById('exportAllBtn')?.addEventListener('click', function() {
        alert('Exportando todos los reportes...');
    });
    
    // Programar reporte
    document.getElementById('scheduleReportBtn')?.addEventListener('click', function() {
        const modal = document.getElementById('scheduleModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });
    
    function closeScheduleModal() {
        const modal = document.getElementById('scheduleModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    function saveSchedule() {
        const reportType = document.getElementById('scheduleReportType').value;
        const frequency = document.getElementById('scheduleFrequency').value;
        const email = document.getElementById('scheduleEmail').value;
        
        alert(`Reporte programado:\nTipo: ${reportType}\nFrecuencia: ${frequency}\nCorreo: ${email || 'no especificado'}`);
        closeScheduleModal();
    }
    
    // Aplicar filtro
    document.getElementById('applyFilterBtn')?.addEventListener('click', function() {
        const period = document.getElementById('periodSelect').value;
        if (period === 'custom') {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            alert(`Aplicando filtro: ${start} al ${end}`);
        } else {
            alert(`Aplicando filtro: ${period}`);
        }
    });
    
    // Limpiar historial
    document.getElementById('clearHistoryBtn')?.addEventListener('click', function() {
        if (confirm('¿Estás seguro de que deseas limpiar todo el historial de reportes?')) {
            const items = document.querySelectorAll('.divide-y .group');
            items.forEach(item => {
                item.style.opacity = '0';
                setTimeout(() => item.remove(), 300);
            });
            setTimeout(() => {
                alert('Historial limpiado');
            }, 300);
        }
    });
    
    // Ver todos los reportes
    document.getElementById('viewAllReports')?.addEventListener('click', function() {
        alert('Mostrando todos los reportes...');
    });
    
    // Cerrar modal al hacer clic fuera
    document.getElementById('scheduleModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeScheduleModal();
    });
</script>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    .animate-fade-in {
        animation: fadeIn 0.2s ease-out;
    }
    
    /* Transición para eliminar reportes */
    .group {
        transition: opacity 0.3s ease-out;
    }
</style>
@endsection