@extends('layouts.dashboard')

@section('title', 'Seguimiento Médico - Clínica Aguachica')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900">Seguimiento Médico</h1>
                <p class="text-gray-500 mt-1">Control y seguimiento de pacientes crónicos</p>
            </div>
            <button onclick="openControlModal()"
                class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus w-5 h-5"></i>
                Nuevo Control
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

        <!-- Stats Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-700 mb-1">Controles Hoy</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $estadisticas['controles_hoy'] }}</p>
                    </div>
                    <i class="fas fa-calendar-day text-blue-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-yellow-700 mb-1">Alertas sin Leer</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ $estadisticas['sin_leer'] }}</p>
                    </div>
                    <i class="fas fa-bell text-yellow-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-red-700 mb-1">Críticas sin Leer</p>
                        <p class="text-2xl font-bold text-red-900">{{ $estadisticas['criticas'] }}</p>
                    </div>
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-700 mb-1">En Seguimiento</p>
                        <p class="text-2xl font-bold text-green-900">{{ $estadisticas['en_seguimiento'] }}</p>
                    </div>
                    <i class="fas fa-heartbeat text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form action="{{ route('dashboard.seguimiento') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Buscar por paciente o documento..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <select name="estado" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los estados</option>
                    @foreach (['Al día', 'Pendiente', 'Urgente'] as $valor)
                        <option value="{{ $valor }}" @selected(request('estado') === $valor)>{{ $valor }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Tracking Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enfermedad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Último Control</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Próximo Control</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médico</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $estados = [
                                'Al día' => ['chip' => 'bg-green-100 text-green-800', 'icono' => 'fa-check-circle'],
                                'Pendiente' => ['chip' => 'bg-yellow-100 text-yellow-800', 'icono' => 'fa-clock'],
                                'Urgente' => ['chip' => 'bg-red-100 text-red-800', 'icono' => 'fa-exclamation-triangle'],
                            ];
                        @endphp

                        @forelse($pacientes as $paciente)
                            @php
                                $ultimoControl = $paciente->historiasClinicas->first();
                                $proximaCita = $paciente->citas()
                                    ->where('fecha_cita', '>=', now()->toDateString())
                                    ->whereIn('estado', ['Programada', 'Confirmada'])
                                    ->orderBy('fecha_cita')
                                    ->min('fecha_cita');

                                $estado = $paciente->alertas_criticas_sin_leer > 0
                                    ? 'Urgente'
                                    : ($paciente->alertas_sin_leer > 0 ? 'Pendiente' : 'Al día');
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user-circle text-blue-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $paciente->nombre_completo }}</span>
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                {{ $paciente->documento_identidad }} •
                                                {{ $paciente->edad ? $paciente->edad . ' años' : 'Sin edad' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($paciente->enfermedades->isEmpty())
                                        <span class="text-gray-400">—</span>
                                    @else
                                        <span class="text-gray-700">{{ $paciente->enfermedades->first()->nombre }}</span>
                                        @if ($paciente->enfermedades->count() > 1)
                                            <span class="ml-1 inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                                +{{ $paciente->enfermedades->count() - 1 }}
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                    {{ $ultimoControl?->fecha_atencion?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($proximaCita)
                                        <span class="font-medium text-gray-700">{{ \Illuminate\Support\Carbon::parse($proximaCita)->format('d/m/Y') }}</span>
                                        @php $dias = \Illuminate\Support\Carbon::today()->diffInDays(\Illuminate\Support\Carbon::parse($proximaCita)); @endphp
                                        @if ($dias <= 3)
                                            <span class="text-xs text-orange-600 ml-1">({{ $dias }} días)</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $estados[$estado]['chip'] }}">
                                        <i class="fas {{ $estados[$estado]['icono'] }} mr-1 text-xs self-center"></i>
                                        {{ $estado }}
                                        @if ($paciente->alertas_sin_leer > 0)
                                            <span class="ml-1 font-bold">({{ $paciente->alertas_sin_leer }})</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($ultimoControl?->medico)
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                <span class="text-purple-600 text-xs font-medium">
                                                    {{ strtoupper(substr($ultimoControl->medico->nombre, 0, 1)) }}
                                                </span>
                                            </div>
                                            <span class="text-gray-700">{{ $ultimoControl->medico->nombre_completo }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <button onclick="viewPaciente({{ $paciente->id }})"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group" title="Ver detalle">
                                            <i class="fas fa-eye text-blue-600 group-hover:text-blue-700"></i>
                                        </button>
                                        <button onclick="openControlModal({{ $paciente->id }}, {{ json_encode($paciente->nombre_completo) }})"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group" title="Registrar control">
                                            <i class="fas fa-stethoscope text-green-600 group-hover:text-green-700"></i>
                                        </button>
                                        @if ($ultimoControl)
                                            <button onclick="editControl({{ $paciente->id }})"
                                                class="p-2 hover:bg-gray-100 rounded-lg transition-colors group" title="Editar último control">
                                                <i class="fas fa-edit text-purple-600 group-hover:text-purple-700"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-stethoscope text-4xl text-gray-300 mb-3 block"></i>
                                    @if (request('search') || request('estado'))
                                        <p>No se encontraron pacientes con esos criterios</p>
                                        <a href="{{ route('dashboard.seguimiento') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-700 font-medium">
                                            Limpiar filtros
                                        </a>
                                    @else
                                        <p>No hay pacientes en seguimiento</p>
                                        <button onclick="openControlModal()" class="mt-3 text-blue-600 hover:text-blue-700 font-medium">
                                            Registrar el primer control
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pacientes->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-500">
                            Mostrando {{ $pacientes->firstItem() }}-{{ $pacientes->lastItem() }} de {{ $pacientes->total() }} pacientes
                        </p>
                        {{ $pacientes->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para Registrar/Editar Control -->
    <div id="controlModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Nuevo Control</h3>
                <button type="button" onclick="closeControlModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>

            <form id="controlForm" method="POST" action="{{ route('dashboard.seguimiento.store') }}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div id="pacienteField">
                    <label for="paciente_id" class="block text-sm font-medium text-gray-700 mb-1">Paciente *</label>
                    <select id="paciente_id" name="paciente_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar paciente</option>
                        @foreach ($pacientesActivos as $pacienteOpcion)
                            <option value="{{ $pacienteOpcion->id }}" @selected((string) old('paciente_id') === (string) $pacienteOpcion->id)>
                                {{ $pacienteOpcion->nombre_completo }} ({{ $pacienteOpcion->documento_identidad }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="pacienteEdit" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paciente</label>
                    <p id="pacienteEditNombre" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-700 font-medium"></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="fecha_atencion" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Atención *</label>
                        <input type="date" id="fecha_atencion" name="fecha_atencion"
                            value="{{ old('fecha_atencion', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="hora_atencion" class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                        <input type="time" id="hora_atencion" name="hora_atencion"
                            value="{{ old('hora_atencion', now()->format('H:i')) }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="tipo_atencion" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Atención *</label>
                        <select id="tipo_atencion" name="tipo_atencion" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach (['Control', 'Urgencias', 'Primera vez'] as $valor)
                                <option value="{{ $valor }}" @selected(old('tipo_atencion', 'Control') === $valor)>{{ $valor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado de la Historia *</label>
                        <select id="estado" name="estado" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach (['Abierta', 'En seguimiento', 'Cerrada'] as $valor)
                                <option value="{{ $valor }}" @selected(old('estado', 'Abierta') === $valor)>{{ $valor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="motivo_consulta" class="block text-sm font-medium text-gray-700 mb-1">Motivo de Consulta *</label>
                        <textarea id="motivo_consulta" name="motivo_consulta" rows="2" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('motivo_consulta') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="impresion_diagnostica" class="block text-sm font-medium text-gray-700 mb-1">Impresión Diagnóstica *</label>
                        <textarea id="impresion_diagnostica" name="impresion_diagnostica" rows="2" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('impresion_diagnostica') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="plan_manipulacion" class="block text-sm font-medium text-gray-700 mb-1">Plan de Manipulación</label>
                        <textarea id="plan_manipulacion" name="plan_manipulacion" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('plan_manipulacion') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="indicaciones" class="block text-sm font-medium text-gray-700 mb-1">Indicaciones</label>
                        <textarea id="indicaciones" name="indicaciones" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('indicaciones') }}</textarea>
                    </div>

                    <div class="md:col-span-2 mt-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Signos Vitales (opcionales)</label>
                        <div class="h-px bg-gray-200 my-2"></div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <input type="number" step="any" name="presion_sistolica" value="{{ old('presion_sistolica') }}"
                                placeholder="Presión sistólica" title="mmHg"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="number" step="any" name="presion_diastolica" value="{{ old('presion_diastolica') }}"
                                placeholder="Presión diastólica" title="mmHg"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="number" step="any" name="frecuencia_cardiaca" value="{{ old('frecuencia_cardiaca') }}"
                                placeholder="Frec. cardíaca" title="lpm"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="number" step="any" name="frecuencia_respiratoria" value="{{ old('frecuencia_respiratoria') }}"
                                placeholder="Frec. respiratoria" title="rpm"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="number" step="any" name="temperatura" value="{{ old('temperatura') }}"
                                placeholder="Temperatura" title="°C"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="number" step="any" name="peso" value="{{ old('peso') }}"
                                placeholder="Peso" title="kg"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="number" step="any" name="talla" value="{{ old('talla') }}"
                                placeholder="Talla" title="cm"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="number" step="any" name="glucosa" value="{{ old('glucosa') }}"
                                placeholder="Glucosa" title="mg/dL"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="number" step="any" name="saturacion_oxigeno" value="{{ old('saturacion_oxigeno') }}"
                                placeholder="Saturación O₂" title="%"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeControlModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i> Guardar Control
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Ver Detalles del Paciente en Seguimiento -->
    <div id="viewTrackingModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Detalle del Seguimiento</h3>
                <button type="button" onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>
            <div class="p-6" id="trackingDetails">
                <!-- Se carga desde el servidor -->
            </div>
        </div>
    </div>

    @php
        $datosJson = $pacientes->getCollection()->map(function ($p) {
            $historia = $p->historiasClinicas->first();
            $signos = $historia?->signosVitales?->first();

            return [
                'id' => $p->id,
                'nombre_completo' => $p->nombre_completo,
                'control' => $historia ? [
                    'id' => $historia->id,
                    'fecha_atencion' => $historia->fecha_atencion?->toDateString(),
                    'hora_atencion' => substr((string) $historia->hora_atencion, 0, 5),
                    'tipo_atencion' => $historia->tipo_atencion,
                    'motivo_consulta' => $historia->motivo_consulta,
                    'impresion_diagnostica' => $historia->impresion_diagnostica,
                    'plan_manipulacion' => $historia->plan_manipulacion,
                    'indicaciones' => $historia->indicaciones,
                    'estado' => $historia->estado,
                    'signos' => $signos ? [
                        'presion_sistolica' => $signos->presion_sistolica,
                        'presion_diastolica' => $signos->presion_diastolica,
                        'frecuencia_cardiaca' => $signos->frecuencia_cardiaca,
                        'frecuencia_respiratoria' => $signos->frecuencia_respiratoria,
                        'temperatura' => $signos->temperatura,
                        'peso' => $signos->peso,
                        'talla' => $signos->talla,
                        'imc' => $signos->imc,
                        'glucosa' => $signos->glucosa,
                        'saturacion_oxigeno' => $signos->saturacion_oxigeno,
                    ] : null,
                ] : null,
            ];
        })->values();
    @endphp

    <script type="application/json" id="seguimiento-data">{!! json_encode($datosJson, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

    <script>
        const seguimientoBaseURL = @json(route('dashboard.seguimiento'));
        const seguimientoData = Object.fromEntries(
            JSON.parse(document.getElementById('seguimiento-data').textContent)
                .map((p) => [p.id, p])
        );
        const camposSignos = [
            'presion_sistolica', 'presion_diastolica', 'frecuencia_cardiaca', 'frecuencia_respiratoria',
            'temperatura', 'peso', 'talla', 'glucosa', 'saturacion_oxigeno',
        ];
        const camposHistoria = [
            'fecha_atencion', 'hora_atencion', 'tipo_atencion', 'motivo_consulta',
            'impresion_diagnostica', 'plan_manipulacion', 'indicaciones', 'estado',
        ];

        function openControlModal(pacienteId = null, pacienteNombre = null) {
            const form = document.getElementById('controlForm');
            form.reset();
            document.getElementById('modalTitle').textContent = 'Nuevo Control';
            document.getElementById('formMethod').value = 'POST';
            form.action = seguimientoBaseURL;
            document.getElementById('pacienteField').classList.remove('hidden');
            document.getElementById('pacienteEdit').classList.add('hidden');

            document.getElementById('fecha_atencion').value = @json(now()->toDateString());
            document.getElementById('hora_atencion').value = @json(now()->format('H:i'));

            if (pacienteId) {
                document.getElementById('paciente_id').value = String(pacienteId);
            }
            document.getElementById('controlModal').classList.remove('hidden');
            document.getElementById('controlModal').classList.add('flex');
        }

        function closeControlModal() {
            const modal = document.getElementById('controlModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function closeViewModal() {
            const modal = document.getElementById('viewTrackingModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function editControl(pacienteId) {
            const data = seguimientoData[pacienteId];
            if (!data || !data.control) return;
            const control = data.control;

            openControlModal();
            document.getElementById('modalTitle').textContent = 'Editar Control';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('controlForm').action = seguimientoBaseURL + '/controles/' + control.id;

            document.getElementById('pacienteField').classList.add('hidden');
            document.getElementById('pacienteEdit').classList.remove('hidden');
            document.getElementById('pacienteEditNombre').textContent = data.nombre_completo;

            camposHistoria.forEach((campo) => {
                const el = document.getElementById(campo);
                if (el) el.value = control[campo] ?? '';
            });
            camposSignos.forEach((campo) => {
                const el = document.querySelector(`#controlForm input[name="${campo}"]`);
                if (el) el.value = control.signos?.[campo] ?? '';
            });
        }

        function escapeHtml(texto) {
            const div = document.createElement('div');
            div.textContent = texto ?? '';
            return div.innerHTML;
        }

        function dato(label, valor) {
            return `<div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">${label}</p>
                <p class="font-medium">${valor ? escapeHtml(String(valor)) : '<span class="text-gray-400">—</span>'}</p>
            </div>`;
        }

        async function viewPaciente(id) {
            const modal = document.getElementById('viewTrackingModal');
            const details = document.getElementById('trackingDetails');
            details.innerHTML = '<p class="text-gray-500 text-center py-6">Cargando...</p>';
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            try {
                const response = await fetch(seguimientoBaseURL + '/pacientes/' + id, {
                    headers: { 'Accept': 'application/json' },
                });
                if (!response.ok) throw new Error('No se pudo cargar el seguimiento');
                const p = await response.json();
                const historia = (p.historias_clinicas ?? [])[0] ?? null;
                const signos = historia ? (historia.signos_vitales ?? [])[0] ?? null : null;
                const alertas = p.alertas ?? [];

                const seccionAlertas = alertas.length
                    ? `<div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm text-red-700 font-medium mb-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Alertas sin leer (${alertas.length})
                        </p>
                        <div class="space-y-2">
                            ${alertas.map((a) => `
                                <div class="flex items-start justify-between gap-3 bg-white rounded-lg p-3 border border-red-100">
                                    <div class="text-sm">
                                        <p class="font-medium ${a.nivel_severidad === 'Crítico' ? 'text-red-700' : 'text-yellow-700'}">
                                            ${escapeHtml(a.mensaje)}
                                        </p>
                                        <p class="text-gray-500 mt-0.5">
                                            ${escapeHtml(a.parametro)}: ${a.valor_detectado ?? '—'}
                                            (ref. ${a.valor_referencia_min ?? '—'} – ${a.valor_referencia_max ?? '—'}) • ${escapeHtml(a.nivel_severidad)}
                                        </p>
                                    </div>
                                    <form method="POST" action="${seguimientoBaseURL}/alertas/${a.id}/leer">
                                        <input type="hidden" name="_method" value="PUT">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 whitespace-nowrap font-medium">
                                            Marcar leída
                                        </button>
                                    </form>
                                </div>`).join('')}
                        </div>
                    </div>`
                    : '';

                const seccionSignos = signos
                    ? `<div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-2">Signos vitales del último control</p>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            ${[
                                ['PA', signos.presion_sistolica ? `${signos.presion_sistolica}/${signos.presion_diastolica}` : null, 'mmHg'],
                                ['F.C.', signos.frecuencia_cardiaca, 'lpm'],
                                ['F.R.', signos.frecuencia_respiratoria, 'rpm'],
                                ['Temp.', signos.temperatura, '°C'],
                                ['Peso', signos.peso, 'kg'],
                                ['Talla', signos.talla, 'cm'],
                                ['IMC', signos.imc, ''],
                                ['Glucosa', signos.glucosa, 'mg/dL'],
                                ['Sat. O₂', signos.saturacion_oxigeno, '%'],
                            ].map(([label, valor, unidad]) => `
                                <div class="bg-white rounded p-2 border border-gray-200">
                                    <p class="text-xs text-gray-500">${label}</p>
                                    <p class="font-medium">${valor ? `${valor} ${unidad}` : '—'}</p>
                                </div>`).join('')}
                        </div>
                    </div>`
                    : '';

                details.innerHTML = `
                    <div class="space-y-6">
                        <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-circle text-blue-600 text-3xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">${escapeHtml(p.nombres + ' ' + p.apellidos)}</h2>
                                <p class="text-gray-500">${escapeHtml(p.tipo_documento)}. ${escapeHtml(p.documento_identidad)}${p.edad ? ` • ${p.edad} años` : ''}</p>
                            </div>
                        </div>

                        ${seccionAlertas}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            ${dato('Último control', historia ? `${historia.fecha_atencion?.substring(0, 10)} • ${historia.tipo_atencion}` : null)}
                            ${dato('Estado historia', historia?.estado)}
                            ${dato('Médico', historia?.medico ? `${historia.medico.nombre} ${historia.medico.apellidos}` : null)}
                            ${dato('Enfermedades', (p.enfermedades ?? []).map((e) => e.nombre).join(', '))}
                            <div class="md:col-span-2 bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Motivo de consulta</p>
                                <p class="text-gray-700">${historia?.motivo_consulta ? escapeHtml(historia.motivo_consulta) : '<span class="text-gray-400">—</span>'}</p>
                            </div>
                            <div class="md:col-span-2 bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Impresión diagnóstica</p>
                                <p class="text-gray-700">${historia?.impresion_diagnostica ? escapeHtml(historia.impresion_diagnostica) : '<span class="text-gray-400">—</span>'}</p>
                            </div>
                            <div class="md:col-span-2 bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Plan / Indicaciones</p>
                                <p class="text-gray-700">${historia?.plan_manipulacion ? escapeHtml(historia.plan_manipulacion) : '<span class="text-gray-400">—</span>'}${historia?.indicaciones ? `<br>${escapeHtml(historia.indicaciones)}` : ''}</p>
                            </div>
                        </div>

                        ${seccionSignos}
                    </div>
                `;
            } catch (error) {
                details.innerHTML = `<p class="text-red-600 text-center py-6">${escapeHtml(error.message)}</p>`;
            }
        }

        // Repoblar con old() si hubo errores de validación
        @if ($errors->any())
            (function () {
                const old = {!! json_encode(collect(old())->only(array_merge(
                    ['paciente_id'], $camposHistoriaJs = ['fecha_atencion', 'hora_atencion', 'tipo_atencion', 'motivo_consulta', 'impresion_diagnostica', 'plan_manipulacion', 'indicaciones', 'estado', 'presion_sistolica', 'presion_diastolica', 'frecuencia_cardiaca', 'frecuencia_respiratoria', 'temperatura', 'peso', 'talla', 'glucosa', 'saturacion_oxigeno'],
                ))->map(fn ($v) => is_scalar($v) || $v === null ? $v : '')->all(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
                Object.entries(old).forEach(([campo, valor]) => {
                    if (valor === null || valor === '') return;
                    const el = document.getElementById(campo)
                        ?? document.querySelector(`#controlForm [name="${campo}"]`);
                    if (el) el.value = valor;
                });
                openControlModal();
            })();
        @endif

        // Cerrar modales al hacer clic fuera
        document.getElementById('controlModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeControlModal();
        });
        document.getElementById('viewTrackingModal')?.addEventListener('click', function (e) {
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
