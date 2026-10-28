@extends('layouts.dashboard')

@section('title', 'Enfermedades - Clínica Aguachica')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900">Enfermedades</h1>
                <p class="text-gray-500 mt-1">Catálogo de enfermedades crónicas</p>
            </div>
            <button onclick="openDiseaseModal()"
                class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus w-5 h-5"></i>
                Nueva Enfermedad
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

        <!-- Search Bar -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form action="{{ route('dashboard.enfermedades') }}" method="GET" class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" placeholder="Buscar por nombre o código CIE-10..."
                    value="{{ request('search') }}"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </form>
        </div>

        <!-- Stats Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4">
                <p class="text-sm text-blue-700 mb-1">Total Enfermedades</p>
                <p class="text-2xl font-bold text-blue-900">{{ number_format($estadisticas['total']) }}</p>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4">
                <p class="text-sm text-green-700 mb-1">Activas</p>
                <p class="text-2xl font-bold text-green-900">{{ number_format($estadisticas['activas']) }}</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4">
                <p class="text-sm text-purple-700 mb-1">Total Pacientes Asociados</p>
                <p class="text-2xl font-bold text-purple-900">{{ number_format($estadisticas['pacientes']) }}</p>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4">
                <p class="text-sm text-orange-700 mb-1">Más Común</p>
                <p class="text-lg font-bold text-orange-900">
                    {{ $estadisticas['mas_comun']?->nombre ?? '—' }}
                </p>
            </div>
        </div>

        <!-- Diseases Grid -->
        @php
            $paleta = [
                'blue' => 'bg-blue-100 text-blue-600',
                'red' => 'bg-red-100 text-red-600',
                'green' => 'bg-green-100 text-green-600',
                'orange' => 'bg-orange-100 text-orange-600',
                'purple' => 'bg-purple-100 text-purple-600',
                'indigo' => 'bg-indigo-100 text-indigo-600',
                'teal' => 'bg-teal-100 text-teal-600',
                'gray' => 'bg-gray-100 text-gray-600',
            ];
            $colores = array_keys($paleta);
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($enfermedades as $indice => $enfermedad)
                @php $color = $colores[$indice % count($colores)]; @endphp
                <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all group hover:-translate-y-1">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 rounded-lg {{ $paleta[$color] }}">
                            <i class="fas fa-heartbeat text-xl"></i>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $enfermedad->codigo_cie10 ?: 'Sin CIE-10' }}
                            </span>
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                {{ $enfermedad->es_activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $enfermedad->es_activo ? 'Activa' : 'Inactiva' }}
                            </span>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $enfermedad->nombre }}</h3>
                    <div class="flex items-center gap-2 text-gray-600 mb-3">
                        <i class="fas fa-chart-bar w-4 h-4"></i>
                        <span class="text-sm">{{ number_format($enfermedad->pacientes_count) }} pacientes registrados</span>
                    </div>
                    @if ($enfermedad->descripcion)
                        <p class="text-sm text-gray-500 line-clamp-2">{{ \Illuminate\Support\Str::limit($enfermedad->descripcion, 120) }}</p>
                    @endif
                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <button type="button" onclick="viewEnfermedad({{ $enfermedad->id }})"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 group">
                            Ver detalles
                            <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition-transform"></i>
                        </button>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="editEnfermedad({{ $enfermedad->id }})"
                                class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="Editar enfermedad">
                                <i class="fas fa-edit w-4 h-4 text-green-600"></i>
                            </button>
                            <form method="POST" action="{{ route('dashboard.enfermedades.destroy', $enfermedad) }}"
                                onsubmit="return confirm('¿Eliminar la enfermedad {{ $enfermedad->nombre }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                                    title="Eliminar enfermedad">
                                    <i class="fas fa-trash w-4 h-4 text-red-600"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-xl border border-gray-200">
                    <i class="fas fa-activity text-4xl text-gray-300 mb-3 block"></i>
                    @if (request('search'))
                        <p class="text-gray-500">No se encontraron enfermedades con esa búsqueda</p>
                        <a href="{{ route('dashboard.enfermedades') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-700 font-medium">
                            Limpiar búsqueda
                        </a>
                    @else
                        <p class="text-gray-500">No hay enfermedades registradas</p>
                        <button onclick="openDiseaseModal()" class="mt-3 text-blue-600 hover:text-blue-700 font-medium">
                            Crear la primera enfermedad
                        </button>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        @if ($enfermedades->hasPages())
            <div class="bg-white rounded-xl border border-gray-200 px-6 py-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-500">
                        Mostrando {{ $enfermedades->firstItem() }}-{{ $enfermedades->lastItem() }} de {{ $enfermedades->total() }} enfermedades
                    </p>
                    {{ $enfermedades->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Modal para Registrar/Editar Enfermedad -->
    <div id="diseaseModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Nueva Enfermedad</h3>
                <button type="button" onclick="closeDiseaseModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>

            <form id="diseaseForm" method="POST" action="{{ route('dashboard.enfermedades.store') }}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Enfermedad *</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Ej: Diabetes Mellitus Tipo 2">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="codigo_cie10" class="block text-sm font-medium text-gray-700 mb-1">Código CIE-10</label>
                        <input type="text" id="codigo_cie10" name="codigo_cie10" value="{{ old('codigo_cie10') }}" maxlength="10"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ej: E11">
                    </div>

                    <div class="flex items-end">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 pb-2">
                            <input type="checkbox" name="es_activo" value="1"
                                @checked(old('es_activo', true)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Enfermedad activa (visible para nuevos diagnósticos)
                        </label>
                        <input type="hidden" name="es_activo" value="0">
                    </div>
                </div>

                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Descripción detallada de la enfermedad...">{{ old('descripcion') }}</textarea>
                </div>

                <!-- Parámetros de control (repetidor) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Parámetros de Control</label>
                    <p class="text-xs text-gray-500 mb-2">Rangos a monitorear por esta enfermedad (glucosa, presión, peso...).</p>
                    <div id="parametrosContainer" class="space-y-2">
                        @php
                            $filasParametros = old('parametros');
                            if (empty($filasParametros)) {
                                $filasParametros = [['nombre' => '', 'min' => '', 'max' => '', 'unidad' => '']];
                            }
                        @endphp

                        @foreach ($filasParametros as $i => $fila)
                            <div class="parametro-fila grid grid-cols-2 md:grid-cols-4 gap-2 items-end border border-gray-200 rounded-lg p-2">
                                <input type="text" name="parametros[{{ $i }}][nombre]" value="{{ $fila['nombre'] ?? '' }}"
                                    placeholder="Parámetro (ej: Glucosa)"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input type="number" step="any" name="parametros[{{ $i }}][min]" value="{{ $fila['min'] ?? '' }}"
                                    placeholder="Mínimo"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input type="number" step="any" name="parametros[{{ $i }}][max]" value="{{ $fila['max'] ?? '' }}"
                                    placeholder="Máximo"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <div class="flex gap-2">
                                    <input type="text" name="parametros[{{ $i }}][unidad]" value="{{ $fila['unidad'] ?? '' }}"
                                        placeholder="Unidad"
                                        class="flex-1 min-w-0 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="this.closest('.parametro-fila').remove()"
                                        class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Quitar parámetro">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" onclick="addParametroRow()"
                        class="mt-2 text-sm text-blue-600 hover:text-blue-700 font-medium">
                        <i class="fas fa-plus mr-1"></i> Agregar parámetro
                    </button>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeDiseaseModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i> Guardar Enfermedad
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Ver Detalles de la Enfermedad -->
    <div id="viewDiseaseModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Detalles de la Enfermedad</h3>
                <button type="button" onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>
            <div class="p-6" id="diseaseDetails">
                <!-- Los detalles se cargan desde el servidor -->
            </div>
        </div>
    </div>

    @php
        $enfermedadesJson = $enfermedades->getCollection()->map(fn ($e) => [
            'id' => $e->id,
            'nombre' => $e->nombre,
            'codigo_cie10' => $e->codigo_cie10,
            'descripcion' => $e->descripcion,
            'es_activo' => $e->es_activo,
            'parametros_control' => $e->parametros_control,
        ])->values();
    @endphp

    <script type="application/json" id="enfermedades-data">{!! json_encode($enfermedadesJson, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

    <script>
        const enfermedadesBaseURL = @json(route('dashboard.enfermedades'));
        const enfermedadesData = Object.fromEntries(
            JSON.parse(document.getElementById('enfermedades-data').textContent)
                .map((e) => [e.id, e])
        );
        let parametroIndex = 1000;

        function openDiseaseModal() {
            document.getElementById('modalTitle').textContent = 'Nueva Enfermedad';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('diseaseForm').action = enfermedadesBaseURL;
            document.getElementById('diseaseModal').classList.remove('hidden');
            document.getElementById('diseaseModal').classList.add('flex');
        }

        function closeDiseaseModal() {
            const modal = document.getElementById('diseaseModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function closeViewModal() {
            const modal = document.getElementById('viewDiseaseModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function addParametroRow(fila = {}) {
            const container = document.getElementById('parametrosContainer');
            const i = parametroIndex++;
            const row = document.createElement('div');
            row.className = 'parametro-fila grid grid-cols-2 md:grid-cols-4 gap-2 items-end border border-gray-200 rounded-lg p-2';
            row.innerHTML = `
                <input type="text" name="parametros[${i}][nombre]" value="${escapeAttr(fila.nombre ?? '')}"
                    placeholder="Parámetro (ej: Glucosa)"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="number" step="any" name="parametros[${i}][min]" value="${escapeAttr(fila.min ?? '')}"
                    placeholder="Mínimo"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="number" step="any" name="parametros[${i}][max]" value="${escapeAttr(fila.max ?? '')}"
                    placeholder="Máximo"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div class="flex gap-2">
                    <input type="text" name="parametros[${i}][unidad]" value="${escapeAttr(fila.unidad ?? '')}"
                        placeholder="Unidad"
                        class="flex-1 min-w-0 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="button" onclick="this.closest('.parametro-fila').remove()"
                        class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Quitar parámetro">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(row);
        }

        function escapeAttr(texto) {
            return String(texto).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
        }

        function escapeHtml(texto) {
            const div = document.createElement('div');
            div.textContent = texto;
            return div.innerHTML;
        }

        function editEnfermedad(id) {
            const e = enfermedadesData[id];
            if (!e) return;
            openDiseaseModal();
            document.getElementById('modalTitle').textContent = 'Editar Enfermedad';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('diseaseForm').action = enfermedadesBaseURL + '/' + id;
            document.getElementById('nombre').value = e.nombre ?? '';
            document.getElementById('codigo_cie10').value = e.codigo_cie10 ?? '';
            document.getElementById('descripcion').value = e.descripcion ?? '';
            document.querySelector('#diseaseForm input[name="es_activo"][value="1"]').checked = Boolean(e.es_activo);

            const container = document.getElementById('parametrosContainer');
            container.innerHTML = '';
            const parametros = Object.entries(e.parametros_control ?? {});
            if (parametros.length === 0) addParametroRow();
            parametros.forEach(([nombre, valor]) => addParametroRow({
                nombre,
                min: valor.min ?? '',
                max: valor.max ?? '',
                unidad: valor.unidad ?? '',
            }));
        }

        async function viewEnfermedad(id) {
            const modal = document.getElementById('viewDiseaseModal');
            const details = document.getElementById('diseaseDetails');
            details.innerHTML = '<p class="text-gray-500 text-center py-6">Cargando...</p>';
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            try {
                const response = await fetch(enfermedadesBaseURL + '/' + id, {
                    headers: { 'Accept': 'application/json' },
                });
                if (!response.ok) throw new Error('No se pudo cargar la enfermedad');
                const e = await response.json();

                const parametros = Object.entries(e.parametros_control ?? {});
                const tablaParametros = parametros.length
                    ? `<div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-2">Parámetros de control</p>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="py-1 pr-2 font-medium">Parámetro</th>
                                    <th class="py-1 pr-2 font-medium">Mínimo</th>
                                    <th class="py-1 pr-2 font-medium">Máximo</th>
                                    <th class="py-1 font-medium">Unidad</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                ${parametros.map(([nombre, v]) => `
                                    <tr class="border-t border-gray-200">
                                        <td class="py-1.5 pr-2">${escapeHtml(nombre)}</td>
                                        <td class="py-1.5 pr-2">${v.min ?? '—'}</td>
                                        <td class="py-1.5 pr-2">${v.max ?? '—'}</td>
                                        <td class="py-1.5">${escapeHtml(v.unidad ?? '—')}</td>
                                    </tr>`).join('')}
                            </tbody>
                        </table>
                    </div>`
                    : '';

                details.innerHTML = `
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-activity text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">${escapeHtml(e.nombre)}</h2>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    CIE-10: ${escapeHtml(e.codigo_cie10 || 'sin código')}
                                </span>
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium ${e.es_activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}">
                                    ${e.es_activo ? 'Activa' : 'Inactiva'}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Pacientes asociados</p>
                            <p class="font-medium">${Number(e.pacientes_count ?? 0).toLocaleString()} pacientes registrados</p>
                        </div>
                        ${e.descripcion ? `
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Descripción</p>
                            <p class="text-gray-700">${escapeHtml(e.descripcion)}</p>
                        </div>` : ''}
                        ${tablaParametros}
                    </div>
                `;
            } catch (error) {
                details.innerHTML = `<p class="text-red-600 text-center py-6">${escapeHtml(error.message)}</p>`;
            }
        }

        // Repoblar el formulario con old() si hubo errores de validación
        @if ($errors->any())
            (function () {
                const old = {!! json_encode(collect(old())->only(['nombre', 'codigo_cie10', 'descripcion'])->all(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
                if (old.nombre) document.getElementById('nombre').value = old.nombre;
                if (old.codigo_cie10) document.getElementById('codigo_cie10').value = old.codigo_cie10;
                if (old.descripcion) document.getElementById('descripcion').value = old.descripcion;
                document.querySelector('#diseaseForm input[name="es_activo"][value="1"]').checked = old.es_activo === '1';
                openDiseaseModal();
            })();
        @endif

        // Cerrar modales al hacer clic fuera
        document.getElementById('diseaseModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeDiseaseModal();
        });
        document.getElementById('viewDiseaseModal')?.addEventListener('click', function (e) {
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
