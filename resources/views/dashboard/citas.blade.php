@extends('layouts.dashboard')

@section('title', 'Citas Médicas - Clínica Aguachica')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900">Citas Médicas</h1>
                <p class="text-gray-500 mt-1">Programación y gestión de citas</p>
            </div>
            <button onclick="openCitaModal()"
                class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus w-5 h-5"></i>
                Nueva Cita
            </button>
        </div>

        @if (session('success'))
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3">
                <i class="fas fa-check-circle"></i>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3">
                <i class="fas fa-exclamation-circle"></i>
                <p>{{ session('error') }}</p>
            </div>
        @endif

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

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Citas Hoy</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['hoy'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ $estadisticas['ayer'] }} ayer</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pendientes</p>
                        <p class="text-3xl font-bold text-orange-600">{{ $estadisticas['pendientes'] }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-lg">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-orange-600 mt-2">Programadas y confirmadas por venir</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Atendidas</p>
                        <p class="text-3xl font-bold text-green-600">{{ $estadisticas['atendidas'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-green-600 mt-2">Citas completadas</p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form action="{{ route('dashboard.citas') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1 flex gap-2">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ $filtros['search'] ?? '' }}"
                            placeholder="Buscar por paciente, médico o motivo..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Buscar
                    </button>
                </div>
                <div class="flex flex-wrap gap-3">
                    <select name="periodo" onchange="this.form.submit()"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="proximas" @selected(($filtros['periodo'] ?? 'proximas') === 'proximas')>Próximas</option>
                        <option value="hoy" @selected(($filtros['periodo'] ?? '') === 'hoy')>Hoy</option>
                        <option value="pasadas" @selected(($filtros['periodo'] ?? '') === 'pasadas')>Pasadas</option>
                        <option value="todas" @selected(($filtros['periodo'] ?? '') === 'todas')>Todas</option>
                    </select>
                    <select name="estado" onchange="this.form.submit()"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los estados</option>
                        @foreach (['Programada', 'Confirmada', 'Atendida', 'Cancelada', 'No Presentado'] as $valor)
                            <option value="{{ $valor }}" @selected(($filtros['estado'] ?? '') === $valor)>{{ $valor }}</option>
                        @endforeach
                    </select>
                    <select name="medico_id" onchange="this.form.submit()"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los médicos</option>
                        @foreach ($medicos as $medico)
                            <option value="{{ $medico->id }}" @selected((string) ($filtros['medico_id'] ?? '') === (string) $medico->id)>
                                {{ $medico->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('dashboard.citas') }}"
                        class="px-4 py-2 text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-undo-alt mr-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Today's Schedule Highlight -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h3 class="text-lg font-semibold mb-1">Agenda de Hoy</h3>
                    <p class="text-blue-100">{{ now()->format('d/m/Y') }}</p>
                </div>
                <div class="flex gap-6">
                    <div class="text-center">
                        <p class="text-2xl font-bold">{{ $estadisticas['agenda_hoy']['atendidas'] }}</p>
                        <p class="text-xs text-blue-100">Atendidas</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold">{{ $estadisticas['agenda_hoy']['pendientes'] }}</p>
                        <p class="text-xs text-blue-100">Pendientes</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold">{{ $estadisticas['agenda_hoy']['otras'] }}</p>
                        <p class="text-xs text-blue-100">Canceladas / No presentado</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médico</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $estados = [
                                'Programada' => ['chip' => 'bg-orange-100 text-orange-800', 'icono' => 'fa-clock'],
                                'Confirmada' => ['chip' => 'bg-blue-100 text-blue-800', 'icono' => 'fa-check'],
                                'Atendida' => ['chip' => 'bg-green-100 text-green-800', 'icono' => 'fa-check-circle'],
                                'Cancelada' => ['chip' => 'bg-red-100 text-red-800', 'icono' => 'fa-times-circle'],
                                'No Presentado' => ['chip' => 'bg-gray-100 text-gray-700', 'icono' => 'fa-user-slash'],
                            ];
                            $editables = ['Programada', 'Confirmada'];
                        @endphp

                        @forelse($citas as $cita)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                                        <span class="text-gray-700">{{ $cita->fecha_cita?->format('d/m/Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-clock text-gray-400 text-sm"></i>
                                        <span class="font-medium text-gray-700">
                                            {{ substr((string) $cita->hora_inicio, 0, 5) }}{{ $cita->hora_fin ? ' – '.substr((string) $cita->hora_fin, 0, 5) : '' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $cita->paciente?->nombre_completo }}</span>
                                            <p class="text-xs text-gray-500">{{ $cita->paciente?->celular }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-700">{{ $cita->motivo }}</p>
                                    <p class="text-xs text-gray-500">{{ $cita->tipo_cita }}{{ $cita->consultorio ? ' • Consultorio '. $cita->consultorio : '' }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                            <span class="text-purple-600 text-xs font-medium">
                                                {{ strtoupper(substr((string) $cita->medico?->nombre, 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="text-gray-700">{{ $cita->medico?->nombre_completo }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full {{ $estados[$cita->estado]['chip'] }}">
                                        <i class="fas {{ $estados[$cita->estado]['icono'] }} text-xs"></i>
                                        {{ $cita->estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <button onclick="viewCita({{ $cita->id }})"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group" title="Ver detalles">
                                            <i class="fas fa-eye text-blue-600 group-hover:text-blue-700"></i>
                                        </button>
                                        @if (in_array($cita->estado, $editables))
                                            <button onclick="editCita({{ $cita->id }})"
                                                class="p-2 hover:bg-gray-100 rounded-lg transition-colors group" title="Editar cita">
                                                <i class="fas fa-edit text-green-600 group-hover:text-green-700"></i>
                                            </button>
                                            <form method="POST" action="{{ route('dashboard.citas.estado', $cita) }}"
                                                onsubmit="return confirm('¿Cancelar esta cita?')">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="estado" value="Cancelada">
                                                <button type="submit"
                                                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors group" title="Cancelar cita">
                                                    <i class="fas fa-times text-red-600 group-hover:text-red-700"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3 block"></i>
                                    @if (($filtros['search'] ?? '') !== '' || ($filtros['estado'] ?? '') !== '' || ($filtros['medico_id'] ?? '') !== '' || ($filtros['periodo'] ?? 'proximas') !== 'proximas')
                                        <p>No se encontraron citas con esos criterios</p>
                                        <a href="{{ route('dashboard.citas') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-700 font-medium">
                                            Limpiar filtros
                                        </a>
                                    @else
                                        <p>No hay citas programadas</p>
                                        <button onclick="openCitaModal()" class="mt-3 text-blue-600 hover:text-blue-700 font-medium">
                                            Programar primera cita
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($citas->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-500">
                            Mostrando {{ $citas->firstItem() }}-{{ $citas->lastItem() }} de {{ $citas->total() }} citas
                        </p>
                        {{ $citas->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para Nueva/Editar Cita -->
    <div id="citaModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Nueva Cita</h3>
                <button onclick="closeCitaModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>

            <form id="citaForm" method="POST" action="{{ route('dashboard.citas.store') }}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="paciente_id" class="block text-sm font-medium text-gray-700 mb-1">Paciente *</label>
                        <select id="paciente_id" name="paciente_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccionar paciente</option>
                            @foreach ($pacientesActivos as $pacienteOpcion)
                                <option value="{{ $pacienteOpcion->id }}" @selected((string) old('paciente_id') === (string) $pacienteOpcion->id)>
                                    {{ $pacienteOpcion->nombre_completo }} ({{ $pacienteOpcion->documento_identidad }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="medico_id" class="block text-sm font-medium text-gray-700 mb-1">Médico *</label>
                        <select id="medico_id" name="medico_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccionar médico</option>
                            @foreach ($medicos as $medicoOpcion)
                                <option value="{{ $medicoOpcion->id }}" @selected((string) old('medico_id') === (string) $medicoOpcion->id)>
                                    {{ $medicoOpcion->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="fecha_cita" class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                        <input type="date" id="fecha_cita" name="fecha_cita"
                            value="{{ old('fecha_cita', now()->toDateString()) }}" min="{{ now()->toDateString() }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-1">Hora inicio *</label>
                            <input type="time" id="hora_inicio" name="hora_inicio" step="300"
                                value="{{ old('hora_inicio') }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-1">Hora fin</label>
                            <input type="time" id="hora_fin" name="hora_fin" step="300" value="{{ old('hora_fin') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label for="tipo_cita" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Cita *</label>
                        <select id="tipo_cita" name="tipo_cita" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach (['Control', 'Urgencia', 'Primera vez', 'Teleconsulta'] as $valor)
                                <option value="{{ $valor }}" @selected(old('tipo_cita', 'Control') === $valor)>{{ $valor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="consultorio" class="block text-sm font-medium text-gray-700 mb-1">Consultorio</label>
                        <input type="text" id="consultorio" name="consultorio" value="{{ old('consultorio') }}"
                            placeholder="Ej. 203"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="md:col-span-2">
                        <label for="motivo" class="block text-sm font-medium text-gray-700 mb-1">Motivo de la consulta *</label>
                        <textarea id="motivo" name="motivo" rows="2" required placeholder="Describa el motivo de la cita..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('motivo') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Notas adicionales</label>
                        <textarea id="observaciones" name="observaciones" rows="2"
                            placeholder="Instrucciones para el paciente, preparación previa..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('observaciones') }}</textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeCitaModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i> Guardar Cita
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Ver Detalles de la Cita -->
    <div id="viewCitaModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Detalles de la Cita</h3>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>
            <div class="p-6" id="citaDetails">
                <!-- Los detalles se cargan desde el servidor -->
            </div>
        </div>
    </div>

    @php
        $datosJson = $citas->getCollection()->map(fn ($cita) => [
            'id' => $cita->id,
            'paciente_id' => $cita->paciente_id,
            'paciente' => $cita->paciente?->nombre_completo,
            'documento' => $cita->paciente?->documento_identidad,
            'celular' => $cita->paciente?->celular,
            'medico_id' => $cita->medico_id,
            'medico' => $cita->medico?->nombre_completo,
            'fecha_cita' => $cita->fecha_cita?->toDateString(),
            'hora_inicio' => substr((string) $cita->hora_inicio, 0, 5),
            'hora_fin' => $cita->hora_fin ? substr((string) $cita->hora_fin, 0, 5) : null,
            'tipo_cita' => $cita->tipo_cita,
            'consultorio' => $cita->consultorio,
            'motivo' => $cita->motivo,
            'observaciones' => $cita->observaciones,
            'estado' => $cita->estado,
        ])->values();
    @endphp

    <script type="application/json" id="citas-data">{!! json_encode($datosJson, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

    <script>
        const citasBaseURL = @json(route('dashboard.citas'));
        const citasData = Object.fromEntries(
            JSON.parse(document.getElementById('citas-data').textContent)
                .map((c) => [c.id, c])
        );
        const estadosChip = {
            'Programada': 'bg-orange-100 text-orange-800',
            'Confirmada': 'bg-blue-100 text-blue-800',
            'Atendida': 'bg-green-100 text-green-800',
            'Cancelada': 'bg-red-100 text-red-800',
            'No Presentado': 'bg-gray-100 text-gray-700',
        };

        function escapeHtml(texto) {
            const div = document.createElement('div');
            div.textContent = texto ?? '';
            return div.innerHTML;
        }

        function dato(label, valor) {
            return `<div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">${label}</p>
                <p class="font-medium text-gray-900">${valor ? escapeHtml(String(valor)) : '<span class="text-gray-400">—</span>'}</p>
            </div>`;
        }

        function openCitaModal() {
            const form = document.getElementById('citaForm');
            form.reset();
            document.getElementById('modalTitle').textContent = 'Nueva Cita';
            document.getElementById('formMethod').value = 'POST';
            form.action = citasBaseURL;
            document.getElementById('fecha_cita').value = @json(now()->toDateString());
            document.getElementById('citaModal').classList.remove('hidden');
            document.getElementById('citaModal').classList.add('flex');
        }

        function closeCitaModal() {
            const modal = document.getElementById('citaModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function closeViewModal() {
            const modal = document.getElementById('viewCitaModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function editCita(id) {
            const c = citasData[id];
            if (!c) return;

            closeViewModal();
            openCitaModal();
            document.getElementById('modalTitle').textContent = 'Editar Cita';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('citaForm').action = citasBaseURL + '/' + c.id;

            ['paciente_id', 'medico_id', 'fecha_cita', 'hora_inicio', 'hora_fin', 'tipo_cita', 'consultorio', 'motivo', 'observaciones']
                .forEach((campo) => {
                    const el = document.getElementById(campo);
                    if (el) el.value = c[campo] ?? '';
                });
        }

        function formEstado(citaId, estado, etiqueta, clase) {
            return `<form method="POST" action="${citasBaseURL}/${citaId}/estado"
                class="${clase}" onsubmit="return confirm('¿${etiqueta}?')">
                <input type="hidden" name="_method" value="PUT">
                @csrf
                <input type="hidden" name="estado" value="${estado}">
                <button type="submit"><i class="fas ${etiqueta.includes('Cancelar') ? 'fa-times' : 'fa-check'} mr-2"></i>${etiqueta}</button>
            </form>`;
        }

        function viewCita(id) {
            const c = citasData[id];
            if (!c) return;

            const acciones = [];
            if (c.estado === 'Programada') {
                acciones.push(formEstado(id, 'Confirmada', 'Confirmar cita', 'flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors'));
            }
            if (c.estado === 'Confirmada') {
                acciones.push(formEstado(id, 'Atendida', 'Marcar atendida', 'flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors'));
            }
            if (['Programada', 'Confirmada'].includes(c.estado)) {
                acciones.push(formEstado(id, 'No Presentado', 'Marcar como no presentado', 'flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors'));
                acciones.push(formEstado(id, 'Cancelada', '¿Cancelar esta cita?', 'flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors'));
            }

            document.getElementById('citaDetails').innerHTML = `
                <div class="space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar-check text-blue-600 text-3xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">${escapeHtml(c.paciente)}</h2>
                                <p class="text-gray-500">${escapeHtml(c.documento ?? '')}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium rounded-full ${estadosChip[c.estado] ?? 'bg-gray-100 text-gray-700'}">
                            ${escapeHtml(c.estado)}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${dato('📅 Fecha', c.fecha_cita ? c.fecha_cita.split('-').reverse().join('/') : null)}
                        ${dato('⏰ Hora', c.hora_fin ? `${c.hora_inicio} – ${c.hora_fin}` : c.hora_inicio)}
                        ${dato('👨‍⚕️ Médico', c.medico)}
                        ${dato('📞 Contacto', c.celular)}
                        ${dato('🏥 Tipo de cita', c.tipo_cita + (c.consultorio ? ` — Consultorio ${c.consultorio}` : ''))}
                    </div>

                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-sm text-green-600 mb-1"><i class="fas fa-notes-medical mr-1"></i> Motivo de la consulta</p>
                        <p class="text-gray-800">${escapeHtml(c.motivo)}</p>
                        ${c.observaciones ? `<p class="text-sm text-gray-600 mt-2"><span class="font-medium">Notas:</span> ${escapeHtml(c.observaciones)}</p>` : ''}
                    </div>

                    <div class="bg-yellow-50 rounded-lg p-4">
                        <p class="text-sm text-yellow-600 mb-2"><i class="fas fa-bell mr-1"></i> Recordatorio para el paciente</p>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <span>Llegar 15 minutos antes de la cita</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <span>Traer documentos de identificación</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <span>Si toma medicamentos, traer la lista actualizada</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <span>En caso de no poder asistir, cancelar con 24 horas de anticipación</span>
                            </li>
                        </ul>
                    </div>

                    ${acciones.length ? `<div class="flex flex-wrap gap-3">${acciones.join('')}</div>` : ''}
                </div>
            `;

            document.getElementById('viewCitaModal').classList.remove('hidden');
            document.getElementById('viewCitaModal').classList.add('flex');
        }

        // Repoblar con old() si hubo errores de validación
        @if ($errors->any())
            (function () {
                const old = {!! json_encode(collect(old())->only([
                    'paciente_id', 'medico_id', 'fecha_cita', 'hora_inicio', 'hora_fin',
                    'tipo_cita', 'consultorio', 'motivo', 'observaciones',
                ])->map(fn ($v) => is_scalar($v) || $v === null ? $v : '')->all(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
                openCitaModal();
                Object.entries(old).forEach(([campo, valor]) => {
                    if (valor === null || valor === '') return;
                    const el = document.getElementById(campo);
                    if (el) el.value = valor;
                });
            })();
        @endif

        // Cerrar modales al hacer clic fuera
        document.getElementById('citaModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeCitaModal();
        });
        document.getElementById('viewCitaModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeViewModal();
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
    </style>
@endsection
