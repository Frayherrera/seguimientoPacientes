@extends('layouts.dashboard')

@section('title', 'Historial Clínico - Clínica Aguachica')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900">Historial Clínico</h1>
                <p class="text-gray-500 mt-1">Registros médicos electrónicos</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('dashboard.historial.export', request()->query()) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-download w-5 h-5"></i>
                    Exportar CSV
                </a>
                <button onclick="printHistory()"
                    class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-print w-5 h-5"></i>
                    Imprimir
                </button>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-700 mb-1">Total Registros</p>
                        <p class="text-2xl font-bold text-blue-900">{{ number_format($estadisticas['total'], 0, ',', '.') }}</p>
                    </div>
                    <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
                </div>
                <p class="text-xs text-blue-600 mt-2">{{ $estadisticas['este_mes'] }} este mes</p>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-700 mb-1">Pacientes Atendidos</p>
                        <p class="text-2xl font-bold text-green-900">{{ number_format($estadisticas['pacientes'], 0, ',', '.') }}</p>
                    </div>
                    <i class="fas fa-users text-green-600 text-2xl"></i>
                </div>
                <p class="text-xs text-green-600 mt-2">Con al menos una atención</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-700 mb-1">Médicos Activos</p>
                        <p class="text-2xl font-bold text-purple-900">{{ number_format($estadisticas['medicos'], 0, ',', '.') }}</p>
                    </div>
                    <i class="fas fa-user-md text-purple-600 text-2xl"></i>
                </div>
                <p class="text-xs text-purple-600 mt-2">Con registros en el historial</p>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-orange-700 mb-1">Registros Hoy</p>
                        <p class="text-2xl font-bold text-orange-900">{{ $estadisticas['hoy'] }}</p>
                    </div>
                    <i class="fas fa-calendar-day text-orange-600 text-2xl"></i>
                </div>
                <p class="text-xs text-orange-600 mt-2">{{ $estadisticas['ayer'] }} ayer</p>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form action="{{ route('dashboard.historial') }}" method="GET"
                class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1 flex gap-2">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ $filtros['search'] ?? '' }}"
                            placeholder="Buscar por paciente, diagnóstico o médico..."
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
                        <option value="">Todos los períodos</option>
                        @foreach (['hoy' => 'Hoy', 'semana' => 'Esta semana', 'mes' => 'Este mes', 'anio' => 'Este año'] as $valor => $etiqueta)
                            <option value="{{ $valor }}" @selected(($filtros['periodo'] ?? '') === $valor)>{{ $etiqueta }}</option>
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
                    <a href="{{ route('dashboard.historial') }}"
                        class="px-4 py-2 text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-undo-alt mr-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- History Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnóstico</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tratamiento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médico</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($registros as $registro)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                                        <div>
                                            <span class="text-gray-700">{{ $registro->fecha_atencion?->format('d/m/Y') }}</span>
                                            <p class="text-xs text-gray-400">{{ substr((string) $registro->hora_atencion, 0, 5) }} hrs</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $registro->paciente?->nombre_completo }}</span>
                                            <p class="text-xs text-gray-500">{{ $registro->paciente?->documento_identidad }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        <p class="text-sm text-gray-900 font-medium">{{ $registro->impresion_diagnostica }}</p>
                                        <p class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($registro->motivo_consulta, 50) }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-700 max-w-xs">
                                        {{ $registro->plan_manipulacion ?: ($registro->indicaciones ?? '—') }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                            <span class="text-purple-600 text-xs font-medium">
                                                {{ strtoupper(substr((string) $registro->medico?->nombre, 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="text-gray-700">{{ $registro->medico?->nombre_completo }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                        {{ $registro->tipo_atencion }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="viewHistory({{ $registro->id }})"
                                        class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                        title="Ver detalles">
                                        <i class="fas fa-eye text-blue-600 group-hover:text-blue-700"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-folder-open text-4xl text-gray-300 mb-3 block"></i>
                                    @if (($filtros['search'] ?? '') !== '' || ($filtros['periodo'] ?? '') !== '' || ($filtros['medico_id'] ?? '') !== '')
                                        <p>No se encontraron registros con esos criterios</p>
                                        <a href="{{ route('dashboard.historial') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-700 font-medium">
                                            Limpiar filtros
                                        </a>
                                    @else
                                        <p>No hay registros en el historial clínico</p>
                                        <p class="text-sm mt-2">Los controles registrados en Seguimiento aparecerán aquí</p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($registros->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-500">
                            Mostrando {{ $registros->firstItem() }}-{{ $registros->lastItem() }} de {{ $registros->total() }} registros
                        </p>
                        {{ $registros->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para Ver Detalles del Historial -->
    <div id="viewHistoryModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Detalles de la Consulta</h3>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>
            <div class="p-6" id="historyDetails">
                <!-- Los detalles se cargan desde el servidor -->
            </div>
        </div>
    </div>

    <script>
        const historialBaseURL = @json(route('dashboard.historial'));

        function escapeHtml(texto) {
            const div = document.createElement('div');
            div.textContent = texto ?? '';
            return div.innerHTML;
        }

        function closeViewModal() {
            const modal = document.getElementById('viewHistoryModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        async function viewHistory(id) {
            const modal = document.getElementById('viewHistoryModal');
            const details = document.getElementById('historyDetails');
            details.innerHTML = '<p class="text-gray-500 text-center py-6">Cargando...</p>';
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            try {
                const response = await fetch(historialBaseURL + '/' + id, {
                    headers: { 'Accept': 'application/json' },
                });
                if (!response.ok) throw new Error('No se pudo cargar el registro');
                const h = await response.json();
                const signos = (h.signos_vitales ?? [])[0] ?? null;
                const prescripciones = h.prescripciones ?? [];
                const medico = h.medico ?? null;
                const paciente = h.paciente ?? null;

                const seccionMedicacion = prescripciones.length
                    ? `<div class="bg-yellow-50 rounded-lg p-4">
                        <p class="text-sm text-yellow-600 mb-2"><i class="fas fa-pills mr-1"></i> Medicación prescrita</p>
                        <div class="flex flex-wrap gap-2">
                            ${prescripciones.map((p) => `
                                <span class="px-3 py-1 bg-white rounded-full text-sm text-gray-700 border border-yellow-200">
                                    ${escapeHtml(p.medicamento?.nombre ?? 'Medicamento')}
                                    ${p.dosis ? ' — ' + escapeHtml(p.dosis) : ''}
                                    ${p.frecuencia ? ' ' + escapeHtml(p.frecuencia) : ''}
                                    ${p.duracion_dias ? ` (${p.duracion_dias} días)` : ''}
                                    ${p.es_activo ? '' : ' <span class="text-xs text-gray-400">(finalizada)</span>'}
                                </span>`).join('')}
                        </div>
                    </div>`
                    : '';

                const seccionSignos = signos
                    ? `<div class="grid grid-cols-3 gap-4">
                        ${[
                            ['Presión Arterial', signos.presion_sistolica ? `${signos.presion_sistolica}/${signos.presion_diastolica}` : null, 'mmHg'],
                            ['Frec. Cardíaca', signos.frecuencia_cardiaca, 'lpm'],
                            ['Frec. Respiratoria', signos.frecuencia_respiratoria, 'rpm'],
                            ['Temperatura', signos.temperatura, '°C'],
                            ['Peso', signos.peso, 'kg'],
                            ['Talla', signos.talla, 'cm'],
                            ['IMC', signos.imc, ''],
                            ['Glucosa', signos.glucosa, 'mg/dL'],
                            ['Sat. O₂', signos.saturacion_oxigeno, '%'],
                        ].map(([label, valor, unidad]) => `
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500">${label}</p>
                                <p class="font-semibold text-gray-900">${valor ? `${valor} ${unidad}` : '—'}</p>
                            </div>`).join('')}
                    </div>`
                    : '<p class="text-sm text-gray-400 text-center">Sin signos vitales registrados en esta atención</p>';

                details.innerHTML = `
                    <div class="space-y-6">
                        <!-- Cabecera -->
                        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-file-medical text-blue-600 text-3xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">${escapeHtml(paciente?.nombre_completo ?? '')}</h2>
                                    <p class="text-gray-500">
                                        ${escapeHtml(paciente?.tipo_documento ?? '')}. ${escapeHtml(paciente?.documento_identidad ?? '')}
                                        | Fecha: ${h.fecha_atencion?.substring(0, 10)} ${h.hora_atencion ? h.hora_atencion.substring(0, 5) + ' hrs' : ''}
                                        | ${escapeHtml(h.tipo_atencion ?? '')}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full ${h.estado === 'Cerrada' ? 'bg-gray-100 text-gray-700' : 'bg-green-100 text-green-800'}">
                                    ${escapeHtml(h.estado ?? '')}
                                </span>
                            </div>
                        </div>

                        <!-- Información del médico -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-md text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Médico tratante</p>
                                    <p class="font-semibold text-gray-900">${escapeHtml(medico ? medico.nombre + ' ' + medico.apellidos : '')}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Diagnóstico y tratamiento -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <p class="text-sm text-blue-600 mb-2"><i class="fas fa-stethoscope mr-1"></i> Impresión diagnóstica</p>
                                <p class="text-gray-800">${escapeHtml(h.impresion_diagnostica)}</p>
                                <p class="text-sm text-gray-500 mt-2">Motivo: ${escapeHtml(h.motivo_consulta)}</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <p class="text-sm text-green-600 mb-2"><i class="fas fa-notes-medical mr-1"></i> Plan / Indicaciones</p>
                                <p class="text-gray-800">${h.plan_manipulacion ? escapeHtml(h.plan_manipulacion) : '<span class="text-gray-400">—</span>'}</p>
                                ${h.indicaciones ? `<p class="text-sm text-gray-600 mt-2">${escapeHtml(h.indicaciones)}</p>` : ''}
                            </div>
                        </div>

                        <!-- Medicación -->
                        ${seccionMedicacion}

                        <!-- Signos vitales -->
                        <div>
                            <p class="text-sm text-gray-500 mb-2"><i class="fas fa-heartbeat mr-1"></i> Signos vitales</p>
                            ${seccionSignos}
                        </div>

                        <!-- Próxima cita -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm text-blue-600 mb-2"><i class="fas fa-calendar-check mr-1"></i> Próxima cita programada</p>
                            <p class="text-gray-800">${h.proxima_cita ? escapeHtml(h.proxima_cita.fecha_cita) + ' — ' + escapeHtml(h.proxima_cita.estado) : '<span class="text-gray-400">Sin citas programadas</span>'}</p>
                        </div>
                    </div>
                `;
            } catch (error) {
                details.innerHTML = `<p class="text-red-600 text-center py-6">${escapeHtml(error.message)}</p>`;
            }
        }

        function printHistory() {
            window.print();
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('viewHistoryModal')?.addEventListener('click', function (e) {
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

        @media print {
            .fixed,
            .sticky,
            header,
            button {
                display: none !important;
            }

            body {
                background: white;
            }

            .rounded-xl,
            .border {
                border: 1px solid #e5e7eb !important;
            }
        }
    </style>
@endsection
