@extends('layouts.dashboard')

@section('title', 'Usuarios - Clínica Aguachica')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900">Usuarios</h1>
                <p class="text-gray-500 mt-1">Gestión de usuarios del sistema</p>
            </div>
            <button onclick="openUserModal()"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus w-5 h-5"></i>
                Nuevo Usuario
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
            <form action="{{ route('dashboard.usuarios') }}" method="GET" class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" placeholder="Buscar por nombre, apellidos o correo..."
                    value="{{ request('search') }}"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $roleColores = [
                                'blue' => 'bg-blue-100 text-blue-800',
                                'purple' => 'bg-purple-100 text-purple-800',
                                'green' => 'bg-green-100 text-green-800',
                                'orange' => 'bg-orange-100 text-orange-800',
                                'teal' => 'bg-teal-100 text-teal-800',
                                'pink' => 'bg-pink-100 text-pink-800',
                                'indigo' => 'bg-indigo-100 text-indigo-800',
                            ];
                            $roleClases = array_values($roleColores);
                        @endphp

                        @forelse($usuarios as $usuario)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-700 rounded-full flex items-center justify-center shadow-sm">
                                            <span class="text-white font-semibold text-sm">{{ strtoupper(substr($usuario->nombre, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $usuario->nombre_completo }}</span>
                                            @if ($usuario->id === auth()->id())
                                                <span class="ml-2 inline-flex px-1.5 py-0.5 text-xs font-medium rounded bg-gray-100 text-gray-600">Tú</span>
                                            @endif
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                @if ($usuario->especialidad)
                                                    {{ $usuario->especialidad }} • {{ $usuario->registro_medico ?? 'S/R' }} •
                                                @endif
                                                {{ $usuario->telefono ?? 'Sin teléfono' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $usuario->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $color = $roleClases[$loop->index % count($roleClases)]; @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                                        {{ $usuario->role?->nombre ?? 'Sin rol' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                        {{ $usuario->es_activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $usuario->es_activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <button onclick="editUser({{ $usuario->id }})"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group" title="Editar usuario">
                                            <i class="fas fa-edit w-4 h-4 text-blue-600 group-hover:text-blue-700"></i>
                                        </button>
                                        <form method="POST" action="{{ route('dashboard.usuarios.destroy', $usuario) }}"
                                            onsubmit="return confirm('¿Eliminar al usuario {{ $usuario->nombre_completo }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                                title="Eliminar usuario">
                                                <i class="fas fa-trash-alt w-4 h-4 text-red-600 group-hover:text-red-700"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-users text-4xl text-gray-300 mb-3 block"></i>
                                    @if (request('search'))
                                        <p>No se encontraron usuarios con esa búsqueda</p>
                                        <a href="{{ route('dashboard.usuarios') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-700 font-medium">
                                            Limpiar búsqueda
                                        </a>
                                    @else
                                        <p>No hay usuarios registrados</p>
                                        <button onclick="openUserModal()" class="mt-3 text-blue-600 hover:text-blue-700 font-medium">
                                            Crear el primer usuario
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if ($usuarios->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-500">
                            Mostrando {{ $usuarios->firstItem() }}-{{ $usuarios->lastItem() }} de {{ $usuarios->total() }} usuarios
                        </p>
                        {{ $usuarios->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para Crear/Editar Usuario -->
    <div id="userModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Nuevo Usuario</h3>
                <button type="button" onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>

            <form id="userForm" method="POST" action="{{ route('dashboard.usuarios.store') }}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="apellidos" class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                    <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Rol *</label>
                    <select id="role_id" name="role_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar rol</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}" @selected((string) old('role_id') === (string) $rol->id)>
                                {{ $rol->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" value="{{ old('telefono') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="registro_medico" class="block text-sm font-medium text-gray-700 mb-1">Registro Médico</label>
                        <input type="text" id="registro_medico" name="registro_medico" value="{{ old('registro_medico') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label for="especialidad" class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                    <input type="text" id="especialidad" name="especialidad" value="{{ old('especialidad') }}"
                        placeholder="Solo para médicos (ej: Endocrinología)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres. Dejar en blanco para mantener la actual.</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="es_activo" value="1" @checked(old('es_activo', true))
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    Usuario activo (puede iniciar sesión)
                </label>
                <input type="hidden" name="es_activo" value="0">

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeUserModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @php
        $usuariosJson = $usuarios->getCollection()->map(fn ($u) => [
            'id' => $u->id,
            'nombre' => $u->nombre,
            'apellidos' => $u->apellidos,
            'email' => $u->email,
            'role_id' => $u->role_id,
            'telefono' => $u->telefono,
            'especialidad' => $u->especialidad,
            'registro_medico' => $u->registro_medico,
            'es_activo' => $u->es_activo,
        ])->values();
    @endphp

    <script type="application/json" id="usuarios-data">{!! json_encode($usuariosJson, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

    <script>
        const usuariosBaseURL = @json(route('dashboard.usuarios'));
        const usuariosData = Object.fromEntries(
            JSON.parse(document.getElementById('usuarios-data').textContent)
                .map((u) => [u.id, u])
        );

        function openUserModal() {
            document.getElementById('modalTitle').textContent = 'Nuevo Usuario';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('userForm').action = usuariosBaseURL;
            document.getElementById('userModal').classList.remove('hidden');
            document.getElementById('userModal').classList.add('flex');
        }

        function closeUserModal() {
            const modal = document.getElementById('userModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function editUser(id) {
            const u = usuariosData[id];
            if (!u) return;
            openUserModal();
            document.getElementById('modalTitle').textContent = 'Editar Usuario';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('userForm').action = usuariosBaseURL + '/' + id;
            document.getElementById('nombre').value = u.nombre ?? '';
            document.getElementById('apellidos').value = u.apellidos ?? '';
            document.getElementById('email').value = u.email ?? '';
            document.getElementById('role_id').value = u.role_id ?? '';
            document.getElementById('telefono').value = u.telefono ?? '';
            document.getElementById('especialidad').value = u.especialidad ?? '';
            document.getElementById('registro_medico').value = u.registro_medico ?? '';
            document.querySelector('#userForm input[name="es_activo"][value="1"]').checked = Boolean(u.es_activo);
            document.getElementById('password').value = '';
            document.getElementById('password_confirmation').value = '';
        }

        // Repoblar con old() si hubo errores de validación
        @if ($errors->any())
            (function () {
                const old = {!! json_encode(collect(old())->only([
                    'nombre', 'apellidos', 'email', 'role_id', 'telefono', 'especialidad', 'registro_medico',
                ])->map(fn ($v) => is_scalar($v) || $v === null ? $v : '')->all(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
                Object.entries(old).forEach(([campo, valor]) => {
                    const el = document.getElementById(campo);
                    if (el && valor !== null && valor !== '') el.value = valor;
                });
                if (old.role_id) document.getElementById('role_id').value = old.role_id;
                document.querySelector('#userForm input[name="es_activo"][value="1"]').checked = old.es_activo === '1';
                openUserModal();
            })();
        @endif

        // Cerrar modal al hacer clic fuera
        document.getElementById('userModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeUserModal();
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
