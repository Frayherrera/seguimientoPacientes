@extends('layouts.dashboard')

@section('title', 'Pacientes - Clínica Aguachica')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900">Pacientes</h1>
                <p class="text-gray-500 mt-1">Gestión de pacientes crónicos</p>
            </div>
            <button onclick="openPatientModal()"
                class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus w-5 h-5"></i>
                Registrar Paciente
            </button>
        </div>

        @if (session('success'))
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3">
                <i class="fas fa-check-circle"></i>
                <p>{{ session('success') }}</p>
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Pacientes</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($estadisticas['total']) }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pacientes Activos</p>
                        <p class="text-3xl font-bold text-green-600">{{ number_format($estadisticas['activos']) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pacientes Inactivos</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ number_format($estadisticas['inactivos']) }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <i class="fas fa-user-slash text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form action="{{ route('dashboard.pacientes') }}" method="GET" class="relative">
                @if (request('estado'))
                    <input type="hidden" name="estado" value="{{ request('estado') }}">
                @endif
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" placeholder="Buscar pacientes por nombre, documento o correo..."
                    value="{{ request('search') }}"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </form>
        </div>

        <!-- Filter Tabs -->
        <div class="flex flex-wrap gap-2 border-b border-gray-200">
            @foreach (['' => 'Todos', 'Activo' => 'Activos', 'Inactivo' => 'Inactivos', 'Fallecido' => 'Fallecidos'] as $valor => $etiqueta)
                <a href="{{ route('dashboard.pacientes', array_filter(['search' => request('search'), 'estado' => $valor])) }}"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors
                        {{ (request('estado') ?? '') === $valor
                            ? 'text-blue-600 border-blue-600'
                            : 'text-gray-600 border-transparent hover:text-gray-900' }}">
                    {{ $etiqueta }}
                </a>
            @endforeach
        </div>

        <!-- Patients Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Paciente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Enfermedad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Último Control</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pacientes as $paciente)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user-circle w-6 h-6 text-blue-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $paciente->nombre_completo }}</span>
                                            <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5">
                                                @if ($paciente->edad)
                                                    <span>{{ $paciente->edad }} años</span>
                                                    <span>•</span>
                                                @endif
                                                <span>{{ $paciente->celular ?: ($paciente->telefono ?: 'Sin contacto') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                    {{ $paciente->tipo_documento }}. {{ $paciente->documento_identidad }}
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $estadoColores = [
                                            'Activo' => 'bg-green-100 text-green-800',
                                            'Inactivo' => 'bg-yellow-100 text-yellow-800',
                                            'Fallecido' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $estadoColores[$paciente->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $paciente->estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                    {{ $paciente->historias_clinicas_max_fecha_atencion
                                        ? \Illuminate\Support\Carbon::parse($paciente->historias_clinicas_max_fecha_atencion)->format('d/m/Y')
                                        : '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="viewPaciente({{ $paciente->id }})"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                            title="Ver detalles">
                                            <i class="fas fa-eye w-4 h-4 text-blue-600 group-hover:text-blue-700"></i>
                                        </button>
                                        <button type="button" onclick="editPaciente({{ $paciente->id }})"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                            title="Editar paciente">
                                            <i class="fas fa-edit w-4 h-4 text-green-600 group-hover:text-green-700"></i>
                                        </button>
                                        <form method="POST" action="{{ route('dashboard.pacientes.destroy', $paciente) }}"
                                            onsubmit="return confirm('¿Eliminar al paciente {{ $paciente->nombre_completo }}? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                                title="Eliminar paciente">
                                                <i class="fas fa-trash w-4 h-4 text-red-600 group-hover:text-red-700"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-users text-4xl text-gray-300 mb-3 block"></i>
                                    @if (request('search') || request('estado'))
                                        <p>No se encontraron pacientes con esos criterios</p>
                                        <a href="{{ route('dashboard.pacientes') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-700 font-medium">
                                            Limpiar filtros
                                        </a>
                                    @else
                                        <p>No hay pacientes registrados</p>
                                        <button onclick="openPatientModal()" class="mt-3 text-blue-600 hover:text-blue-700 font-medium">
                                            Registrar el primer paciente
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
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

    <!-- Modal para Registrar/Editar Paciente -->
    <div id="patientModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Registrar Paciente</h3>
                <button type="button" onclick="closePatientModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>

            <form id="patientForm" method="POST" action="{{ route('dashboard.pacientes.store') }}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Información Personal</label>
                        <div class="h-px bg-gray-200 my-2"></div>
                    </div>

                    <div>
                        <label for="nombres" class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                        <input type="text" id="nombres" name="nombres" value="{{ old('nombres') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="apellidos" class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                        <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="tipo_documento" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento
                            *</label>
                        <select id="tipo_documento" name="tipo_documento" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach (['CC' => 'Cédula de Ciudadanía', 'CE' => 'Cédula de Extranjería', 'TI' => 'Tarjeta de Identidad', 'PAS' => 'Pasaporte'] as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected(old('tipo_documento', 'CC') === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="documento_identidad" class="block text-sm font-medium text-gray-700 mb-1">Documento de
                            Identidad *</label>
                        <input type="text" id="documento_identidad" name="documento_identidad"
                            value="{{ old('documento_identidad') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 mb-1">Fecha de
                            Nacimiento *</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                            value="{{ old('fecha_nacimiento') }}" required max="{{ now()->toDateString() }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="genero" class="block text-sm font-medium text-gray-700 mb-1">Género *</label>
                        <select id="genero" name="genero" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach (['M' => 'Masculino', 'F' => 'Femenino', 'Otro' => 'Otro'] as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected(old('genero') === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" value="{{ old('telefono') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="celular" class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                        <input type="tel" id="celular" name="celular" value="{{ old('celular') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                        <input type="text" id="direccion" name="direccion" value="{{ old('direccion') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="ciudad" class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                        <input type="text" id="ciudad" name="ciudad" value="{{ old('ciudad') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="departamento" class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                        <input type="text" id="departamento" name="departamento" value="{{ old('departamento') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="eps" class="block text-sm font-medium text-gray-700 mb-1">EPS</label>
                        <input type="text" id="eps" name="eps" value="{{ old('eps') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="ips_primaria" class="block text-sm font-medium text-gray-700 mb-1">IPS Primaria</label>
                        <input type="text" id="ips_primaria" name="ips_primaria" value="{{ old('ips_primaria') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="md:col-span-2 mt-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Información Médica</label>
                        <div class="h-px bg-gray-200 my-2"></div>
                    </div>

                    <div>
                        <label for="enfermedad_id" class="block text-sm font-medium text-gray-700 mb-1">Enfermedad /
                            Diagnóstico</label>
                        <select id="enfermedad_id" name="enfermedad_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Sin enfermedad registrada</option>
                            @foreach ($enfermedades as $enfermedad)
                                <option value="{{ $enfermedad->id }}" @selected((string) old('enfermedad_id') === (string) $enfermedad->id)>
                                    {{ $enfermedad->nombre }} ({{ $enfermedad->codigo_cie10 }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="fecha_diagnostico" class="block text-sm font-medium text-gray-700 mb-1">Fecha de
                            Diagnóstico</label>
                        <input type="date" id="fecha_diagnostico" name="fecha_diagnostico"
                            value="{{ old('fecha_diagnostico') }}" max="{{ now()->toDateString() }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                        <select id="estado" name="estado" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach (['Activo', 'Inactivo', 'Fallecido'] as $valor)
                                <option value="{{ $valor }}" @selected(old('estado', 'Activo') === $valor)>{{ $valor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Notas adicionales sobre el paciente...">{{ old('observaciones') }}</textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closePatientModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i> Guardar Paciente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Ver Detalles del Paciente -->
    <div id="viewPatientModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Detalles del Paciente</h3>
                <button type="button" onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>
            <div class="p-6" id="patientDetails">
                <!-- Los detalles se cargan desde el servidor -->
            </div>
        </div>
    </div>

    @php
        $pacientesJson = $pacientes->getCollection()->map(fn ($p) => [
            'id' => $p->id,
            'documento_identidad' => $p->documento_identidad,
            'tipo_documento' => $p->tipo_documento,
            'nombres' => $p->nombres,
            'apellidos' => $p->apellidos,
            'fecha_nacimiento' => $p->fecha_nacimiento?->toDateString(),
            'genero' => $p->genero,
            'telefono' => $p->telefono,
            'celular' => $p->celular,
            'email' => $p->email,
            'direccion' => $p->direccion,
            'ciudad' => $p->ciudad,
            'departamento' => $p->departamento,
            'eps' => $p->eps,
            'ips_primaria' => $p->ips_primaria,
            'estado' => $p->estado,
            'observaciones' => $p->observaciones,
            'enfermedad_id' => $p->enfermedades->first()?->id,
        ])->values();
    @endphp

    <script type="application/json" id="pacientes-data">{!! json_encode($pacientesJson, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

    <script>
        const pacientesBaseURL = @json(route('dashboard.pacientes'));
        const pacientesData = Object.fromEntries(
            JSON.parse(document.getElementById('pacientes-data').textContent)
                .map((p) => [p.id, p])
        );
        const formFields = [
            'nombres', 'apellidos', 'tipo_documento', 'documento_identidad', 'fecha_nacimiento',
            'genero', 'telefono', 'celular', 'email', 'direccion', 'ciudad', 'departamento',
            'eps', 'ips_primaria', 'estado', 'observaciones', 'enfermedad_id', 'fecha_diagnostico',
        ];

        function openPatientModal() {
            document.getElementById('modalTitle').textContent = 'Registrar Paciente';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('patientForm').action = pacientesBaseURL;
            document.getElementById('patientModal').classList.remove('hidden');
            document.getElementById('patientModal').classList.add('flex');
        }

        function closePatientModal() {
            const modal = document.getElementById('patientModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function closeViewModal() {
            const modal = document.getElementById('viewPatientModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function fillForm(data) {
            formFields.forEach((field) => {
                const el = document.getElementById(field);
                if (el) el.value = data[field] ?? '';
            });
        }

        function editPaciente(id) {
            const paciente = pacientesData[id];
            if (!paciente) return;
            openPatientModal();
            document.getElementById('modalTitle').textContent = 'Editar Paciente';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('patientForm').action = pacientesBaseURL + '/' + id;
            fillForm(paciente);
        }

        function valorOFlecha(valor) {
            return valor
                ? escapeHtml(valor)
                : '<span class="text-gray-400">—</span>';
        }

        function escapeHtml(texto) {
            const div = document.createElement('div');
            div.textContent = texto;
            return div.innerHTML;
        }

        function dato(label, valor) {
            return `<div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500">${label}</p>
                <p class="font-medium">${valorOFlecha(valor)}</p>
            </div>`;
        }

        async function viewPaciente(id) {
            const modal = document.getElementById('viewPatientModal');
            const details = document.getElementById('patientDetails');
            details.innerHTML = '<p class="text-gray-500 text-center py-6">Cargando...</p>';
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            try {
                const response = await fetch(pacientesBaseURL + '/' + id, {
                    headers: { 'Accept': 'application/json' },
                });
                if (!response.ok) throw new Error('No se pudo cargar el paciente');
                const p = await response.json();

                const enfermedades = (p.enfermedades ?? [])
                    .map((e) => escapeHtml(e.nombre))
                    .join(', ');

                details.innerHTML = `
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-circle text-blue-600 text-4xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">${escapeHtml(p.nombres + ' ' + p.apellidos)}</h2>
                            <p class="text-gray-500">${escapeHtml(p.tipo_documento)}. ${escapeHtml(p.documento_identidad)}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${dato('Fecha de Nacimiento', p.fecha_nacimiento ? p.fecha_nacimiento.substring(0, 10) : null)}
                        ${dato('Género', { M: 'Masculino', F: 'Femenino', Otro: 'Otro' }[p.genero] ?? null)}
                        ${dato('Teléfono', p.telefono)}
                        ${dato('Celular', p.celular)}
                        ${dato('Correo Electrónico', p.email)}
                        ${dato('Dirección', p.direccion)}
                        ${dato('Ciudad', p.ciudad)}
                        ${dato('Departamento', p.departamento)}
                        ${dato('EPS', p.eps)}
                        ${dato('IPS Primaria', p.ips_primaria)}
                        ${dato('Enfermedades', enfermedades || null)}
                        ${dato('Estado', p.estado)}
                        <div class="md:col-span-2 bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500">Observaciones</p>
                            <p class="font-medium">${valorOFlecha(p.observaciones)}</p>
                        </div>
                    </div>
                `;
            } catch (error) {
                details.innerHTML = `<p class="text-red-600 text-center py-6">${escapeHtml(error.message)}</p>`;
            }
        }

        // Repoblar el formulario con old() si hubo errores de validación
        @if ($errors->any())
            fillForm({!! json_encode(collect(old())->only([
                'nombres', 'apellidos', 'tipo_documento', 'documento_identidad', 'fecha_nacimiento',
                'genero', 'telefono', 'celular', 'email', 'direccion', 'ciudad', 'departamento',
                'eps', 'ips_primaria', 'estado', 'observaciones', 'enfermedad_id', 'fecha_diagnostico',
            ])->map(fn ($value) => is_scalar($value) || $value === null ? $value : '')->all(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!});
            openPatientModal();
        @endif

        // Cerrar modales al hacer clic fuera
        document.getElementById('patientModal')?.addEventListener('click', function (e) {
            if (e.target === this) closePatientModal();
        });
        document.getElementById('viewPatientModal')?.addEventListener('click', function (e) {
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
