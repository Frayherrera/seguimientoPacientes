@extends('layouts.dashboard')

@section('title', 'Configuración - Clínica Aguachica')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900">Configuración</h1>
                <p class="text-gray-500 mt-1">Gestiona las preferencias del sistema</p>
            </div>
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

        <!-- Secciones de configuración -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Perfil de Usuario -->
            <button type="button" onclick="openPerfilModal()"
                class="text-left bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow group">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-user text-blue-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Perfil de Usuario</h3>
                        <p class="text-sm text-gray-600">Actualiza tu información personal y contraseña</p>
                        <p class="text-xs text-gray-500 mt-2">{{ $usuario->nombre_completo }} · {{ $usuario->email }}</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600 transition-colors mt-2"></i>
                </div>
            </button>

            <!-- Seguridad -->
            <button type="button" onclick="openSesionesModal()"
                class="text-left bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow group">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                        <i class="fas fa-shield-alt text-purple-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Seguridad</h3>
                        <p class="text-sm text-gray-600">Cierra la sesión en todos los demás dispositivos</p>
                        <p class="text-xs text-gray-500 mt-2">Se solicitará tu contraseña actual</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-600 transition-colors mt-2"></i>
                </div>
            </button>
        </div>

        <!-- Umbrales de alertas -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Umbrales de Alerta Clínicas</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Los controles de seguimiento comparan los signos vitales contra estos umbrales y
                            generan alertas automáticamente.
                        </p>
                    </div>
                    <button type="button" onclick="openUmbralModal()"
                        class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus"></i>
                        Nuevo umbral
                    </button>
                </div>
                <form action="{{ route('dashboard.configuracion') }}" method="GET" class="flex gap-3 mt-4">
                    <select name="enfermedad_id" onchange="this.form.submit()"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas las enfermedades</option>
                        @foreach ($tiposEnfermedad as $tipo)
                            <option value="{{ $tipo->id }}" @selected((string) ($filtros['enfermedad_id'] ?? '') === (string) $tipo->id)>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @if (($filtros['enfermedad_id'] ?? '') !== '')
                        <a href="{{ route('dashboard.configuracion') }}"
                            class="px-4 py-2 text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-undo-alt mr-1"></i> Limpiar
                        </a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enfermedad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parámetro</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rango de referencia</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $niveles = [
                                'Bajo' => 'bg-blue-100 text-blue-800',
                                'Medio' => 'bg-orange-100 text-orange-800',
                                'Alto' => 'bg-red-100 text-red-800',
                            ];
                        @endphp

                        @forelse($umbrales as $umbral)
                            <tr class="hover:bg-gray-50 transition-colors {{ $umbral->es_activo ? '' : 'opacity-60' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-medium text-gray-900">{{ $umbral->tipoEnfermedad?->nombre ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $etiquetasParametros[$umbral->nombre_parametro] ?? $umbral->nombre_parametro }}
                                    </p>
                                    @if ($umbral->mensaje_alerta)
                                        <p class="text-xs text-gray-500 max-w-xs truncate" title="{{ $umbral->mensaje_alerta }}">
                                            {{ $umbral->mensaje_alerta }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $umbral->valor_minimo ?? '−' }} – {{ $umbral->valor_maximo ?? '−' }}
                                    <span class="text-gray-500">{{ $umbral->unidad }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $niveles[$umbral->nivel_alerta] }}">
                                        {{ $umbral->nivel_alerta }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $umbral->es_activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $umbral->es_activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="openUmbralModal({{ $umbral->id }})"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group" title="Editar umbral">
                                            <i class="fas fa-edit text-blue-600 group-hover:text-blue-700"></i>
                                        </button>
                                        <form method="POST" action="{{ route('dashboard.configuracion.umbrales.estado', $umbral) }}">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                                title="{{ $umbral->es_activo ? 'Desactivar umbral' : 'Activar umbral' }}">
                                                <i class="fas {{ $umbral->es_activo ? 'fa-toggle-on text-green-600' : 'fa-toggle-off text-gray-400' }}"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('dashboard.configuracion.umbrales.destroy', $umbral) }}"
                                            onsubmit="return confirm('¿Eliminar este umbral? Las alertas ya generadas no se modifican.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 hover:bg-gray-100 rounded-lg transition-colors group" title="Eliminar umbral">
                                                <i class="fas fa-trash-alt text-red-600 group-hover:text-red-700"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-bell-slash text-4xl text-gray-300 mb-3 block"></i>
                                    @if (($filtros['enfermedad_id'] ?? '') !== '')
                                        <p>No hay umbrales configurados para esa enfermedad</p>
                                        <a href="{{ route('dashboard.configuracion') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-700 font-medium">
                                            Ver todas las enfermedades
                                        </a>
                                    @else
                                        <p>No hay umbrales de alerta configurados</p>
                                        <button type="button" onclick="openUmbralModal()" class="mt-3 text-blue-600 hover:text-blue-700 font-medium">
                                            Crear el primer umbral
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para Nuevo/Editar Umbral -->
    <div id="umbralModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900" id="umbralModalTitle">Nuevo Umbral</h3>
                <button type="button" onclick="closeUmbralModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>

            <form id="umbralForm" method="POST" action="{{ route('dashboard.configuracion.umbrales.store') }}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="umbralFormMethod" value="POST">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tipo_enfermedad_id" class="block text-sm font-medium text-gray-700 mb-1">Enfermedad *</label>
                        <select id="tipo_enfermedad_id" name="tipo_enfermedad_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccionar enfermedad</option>
                            @foreach ($tiposEnfermedad as $tipo)
                                <option value="{{ $tipo->id }}" @selected((string) old('tipo_enfermedad_id') === (string) $tipo->id)>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="nombre_parametro" class="block text-sm font-medium text-gray-700 mb-1">Parámetro *</label>
                        <select id="nombre_parametro" name="nombre_parametro" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccionar parámetro</option>
                            @foreach ($etiquetasParametros as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected(old('nombre_parametro') === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="valor_minimo" class="block text-sm font-medium text-gray-700 mb-1">Valor mínimo</label>
                        <input type="number" step="0.01" id="valor_minimo" name="valor_minimo" value="{{ old('valor_minimo') }}"
                            placeholder="Ej. 70"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="valor_maximo" class="block text-sm font-medium text-gray-700 mb-1">Valor máximo</label>
                        <input type="number" step="0.01" id="valor_maximo" name="valor_maximo" value="{{ old('valor_maximo') }}"
                            placeholder="Ej. 180"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="unidad" class="block text-sm font-medium text-gray-700 mb-1">Unidad *</label>
                        <input type="text" id="unidad" name="unidad" value="{{ old('unidad') }}" required maxlength="20"
                            placeholder="Ej. mg/dL, mmHg, lpm"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="nivel_alerta" class="block text-sm font-medium text-gray-700 mb-1">Nivel de alerta *</label>
                        <select id="nivel_alerta" name="nivel_alerta" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach (['Bajo', 'Medio', 'Alto'] as $valor)
                                <option value="{{ $valor }}" @selected(old('nivel_alerta', 'Medio') === $valor)>{{ $valor }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Alto genera alertas Crítico, Medio genera Advertencia, Bajo genera Info.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="mensaje_alerta" class="block text-sm font-medium text-gray-700 mb-1">Mensaje de la alerta</label>
                        <textarea id="mensaje_alerta" name="mensaje_alerta" rows="2" maxlength="500"
                            placeholder="Ej. Glucosa fuera de rango normal para paciente diabético"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('mensaje_alerta') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input type="hidden" name="es_activo" value="0">
                            <input type="checkbox" name="es_activo" value="1" id="es_activo" checked
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Umbral activo (participa en la generación de alertas)
                        </label>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeUmbralModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i> Guardar Umbral
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Perfil -->
    <div id="perfilModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Perfil de Usuario</h3>
                <button type="button" onclick="closePerfilModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('dashboard.configuracion.perfil') }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="perfil_nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" id="perfil_nombre" name="nombre" required maxlength="100"
                            value="{{ old('nombre', $usuario->nombre) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="perfil_apellidos" class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                        <input type="text" id="perfil_apellidos" name="apellidos" required maxlength="100"
                            value="{{ old('apellidos', $usuario->apellidos) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="md:col-span-2">
                        <label for="perfil_email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico *</label>
                        <input type="email" id="perfil_email" name="email" required maxlength="255"
                            value="{{ old('email', $usuario->email) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="perfil_telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" id="perfil_telefono" name="telefono" maxlength="20"
                            value="{{ old('telefono', $usuario->telefono) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="perfil_especialidad" class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                        <input type="text" id="perfil_especialidad" name="especialidad" maxlength="100"
                            value="{{ old('especialidad', $usuario->especialidad) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="md:col-span-2">
                        <label for="perfil_registro_medico" class="block text-sm font-medium text-gray-700 mb-1">Registro médico</label>
                        <input type="text" id="perfil_registro_medico" name="registro_medico" maxlength="50"
                            value="{{ old('registro_medico', $usuario->registro_medico) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 mt-2">
                    <h4 class="font-medium text-gray-900 mb-3">Cambiar Contraseña</h4>
                    <p class="text-xs text-gray-500 mb-3">Déjalo vacío para mantener tu contraseña actual.</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-3">
                            <label for="perfil_password_actual" class="block text-sm font-medium text-gray-700 mb-1">Contraseña actual</label>
                            <input type="password" id="perfil_password_actual" name="password_actual" autocomplete="current-password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="perfil_password" class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
                            <input type="password" id="perfil_password" name="password" autocomplete="new-password" minlength="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="perfil_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                            <input type="password" id="perfil_password_confirmation" name="password_confirmation" autocomplete="new-password" minlength="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closePerfilModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Seguridad -->
    <div id="sesionesModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Seguridad</h3>
                <button type="button" onclick="closeSesionesModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('dashboard.configuracion.sesiones') }}" class="p-6 space-y-4">
                @csrf

                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-blue-800 mb-1"><i class="fas fa-info-circle mr-1"></i> Sesión actual</p>
                    <p class="text-xs text-blue-600">Iniciaste sesión como {{ $usuario->email }}. Esta sesión se mantendrá activa.</p>
                </div>

                <div>
                    <label for="sesiones_password_actual" class="block text-sm font-medium text-gray-700 mb-1">Contraseña actual *</label>
                    <input type="password" id="sesiones_password_actual" name="password_actual" required
                        autocomplete="current-password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeSesionesModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar en otros dispositivos
                    </button>
                </div>
            </form>
        </div>
    </div>

    @php
        $umbralesJson = $umbrales->mapWithKeys(fn ($umbral) => [
            $umbral->id => [
                'tipo_enfermedad_id' => $umbral->tipo_enfermedad_id,
                'nombre_parametro' => $umbral->nombre_parametro,
                'valor_minimo' => $umbral->valor_minimo,
                'valor_maximo' => $umbral->valor_maximo,
                'unidad' => $umbral->unidad,
                'nivel_alerta' => $umbral->nivel_alerta,
                'mensaje_alerta' => $umbral->mensaje_alerta,
                'es_activo' => $umbral->es_activo,
            ],
        ]);
    @endphp

    <script type="application/json" id="umbrales-data">{!! json_encode($umbralesJson, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

    <script>
        const umbralesBaseURL = @json(route('dashboard.configuracion'));
        const umbralesData = JSON.parse(document.getElementById('umbrales-data').textContent);

        function cerrar(id) {
            const modal = document.getElementById(id);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function abrir(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function openUmbralModal(id = null) {
            const form = document.getElementById('umbralForm');
            form.reset();
            document.getElementById('umbralFormMethod').value = 'POST';
            form.action = umbralesBaseURL + '/umbrales';
            document.getElementById('umbralModalTitle').textContent = 'Nuevo Umbral';
            document.getElementById('es_activo').checked = true;

            if (id && umbralesData[id]) {
                const u = umbralesData[id];
                document.getElementById('umbralModalTitle').textContent = 'Editar Umbral';
                document.getElementById('umbralFormMethod').value = 'PUT';
                form.action = umbralesBaseURL + '/umbrales/' + id;

                ['tipo_enfermedad_id', 'nombre_parametro', 'valor_minimo', 'valor_maximo', 'unidad', 'nivel_alerta', 'mensaje_alerta']
                    .forEach((campo) => {
                        const el = document.getElementById(campo);
                        if (el) el.value = u[campo] ?? '';
                    });
                document.getElementById('es_activo').checked = !!u.es_activo;
            }

            abrir('umbralModal');
        }

        function closeUmbralModal() {
            cerrar('umbralModal');
        }

        function openPerfilModal() {
            abrir('perfilModal');
        }

        function closePerfilModal() {
            cerrar('perfilModal');
        }

        function openSesionesModal() {
            abrir('sesionesModal');
        }

        function closeSesionesModal() {
            cerrar('sesionesModal');
        }

        // Repoblar el modal de umbral con old() si hubo errores de validación
        @if ($errors->any() && old('tipo_enfermedad_id'))
            (function () {
                const old = {!! json_encode(collect(old())->only([
                    'tipo_enfermedad_id', 'nombre_parametro', 'valor_minimo', 'valor_maximo',
                    'unidad', 'nivel_alerta', 'mensaje_alerta',
                ])->map(fn ($v) => is_scalar($v) || $v === null ? $v : '')->all(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
                openUmbralModal();
                Object.entries(old).forEach(([campo, valor]) => {
                    if (valor === null || valor === '') return;
                    const el = document.getElementById(campo);
                    if (el) el.value = valor;
                });
                document.getElementById('es_activo').checked = {{ old('es_activo') ? 'true' : 'false' }};
            })();
        @elseif ($errors->any() && old('nombre'))
            openPerfilModal();
        @endif

        // Cerrar modales al hacer clic fuera
        ['umbralModal', 'perfilModal', 'sesionesModal'].forEach((id) => {
            document.getElementById(id)?.addEventListener('click', function (e) {
                if (e.target === this) cerrar(id);
            });
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
