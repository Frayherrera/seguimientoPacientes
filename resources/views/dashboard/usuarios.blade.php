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
        <button id="openUserModalBtn" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus w-5 h-5"></i>
            Nuevo Usuario
        </button>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form action="{}" method="GET" class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text"
                   name="search"
                   placeholder="Buscar usuarios..."
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
                        $usuarios = [
                            ['name' => 'Dr. Carlos Martínez', 'email' => 'carlos@clinica.com', 'role' => 'Médico', 'roleColor' => 'purple', 'status' => 'Activo', 'statusColor' => 'green'],
                            ['name' => 'Ana López', 'email' => 'ana@clinica.com', 'role' => 'Administrador', 'roleColor' => 'blue', 'status' => 'Activo', 'statusColor' => 'green'],
                            ['name' => 'María García', 'email' => 'maria@clinica.com', 'role' => 'Recepción', 'roleColor' => 'orange', 'status' => 'Activo', 'statusColor' => 'green'],
                            ['name' => 'Dr. Juan Pérez', 'email' => 'juan@clinica.com', 'role' => 'Médico', 'roleColor' => 'purple', 'status' => 'Inactivo', 'statusColor' => 'red'],
                            ['name' => 'Laura Rodríguez', 'email' => 'laura@clinica.com', 'role' => 'Enfermera', 'roleColor' => 'green', 'status' => 'Activo', 'statusColor' => 'green'],
                        ];
                    @endphp

                    @forelse($usuarios as $usuario)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-700 rounded-full flex items-center justify-center shadow-sm">
                                        <span class="text-white font-semibold text-sm">{{ strtoupper(substr($usuario['name'], 0, 1)) }}</span>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $usuario['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $usuario['email'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $usuario['roleColor'] === 'purple' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $usuario['roleColor'] === 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $usuario['roleColor'] === 'orange' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $usuario['roleColor'] === 'green' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ $usuario['role'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $usuario['statusColor'] === 'green' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $usuario['statusColor'] === 'red' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $usuario['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <button onclick="editUser('{{ $usuario['email'] }}')" 
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group">
                                        <i class="fas fa-edit w-4 h-4 text-blue-600 group-hover:text-blue-700"></i>
                                    </button>
                                    <button onclick="deleteUser('{{ $usuario['name'] }}')" 
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group">
                                        <i class="fas fa-trash-alt w-4 h-4 text-red-600 group-hover:text-red-700"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-users text-4xl text-gray-300 mb-3 block"></i>
                                <p>No hay usuarios registrados</p>
                                <button onclick="openUserModal()" class="mt-3 text-blue-600 hover:text-blue-700 font-medium">
                                    Crear el primer usuario
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{-- Aquí iría la paginación --}}
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">Mostrando 1-5 de 15 usuarios</p>
                <div class="flex gap-2">
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 disabled:opacity-50" disabled>
                        Anterior
                    </button>
                    <button class="px-3 py-1 bg-blue-600 text-white rounded-lg">1</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">2</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">3</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
                        Siguiente
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Usuario -->
<div id="userModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 transform transition-all">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Nuevo Usuario</h3>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times w-6 h-6 text-xl"></i>
            </button>
        </div>

        <form id="userForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="user_id" id="userId">

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                <select id="role" name="role" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Seleccionar rol</option>
                    <option value="admin">Administrador</option>
                    <option value="medico">Médico</option>
                    <option value="enfermero">Enfermero(a)</option>
                    <option value="recepcion">Recepción</option>
                </select>
            </div>

            <div id="passwordFields">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres. Dejar en blanco para mantener la actual.</p>
                </div>

                <div class="mt-3">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

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

<script>
    function openUserModal() {
        const modal = document.getElementById('userModal');
        const modalTitle = document.getElementById('modalTitle');
        const formMethod = document.getElementById('formMethod');
        const userId = document.getElementById('userId');
        const passwordFields = document.getElementById('passwordFields');
        
        modalTitle.textContent = 'Nuevo Usuario';
        formMethod.value = 'POST';
        userId.value = '';
        document.getElementById('userForm').reset();
        passwordFields.style.display = 'block';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeUserModal() {
        const modal = document.getElementById('userModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function editUser(email) {
        // Aquí cargarías los datos del usuario desde el backend
        const modal = document.getElementById('userModal');
        const modalTitle = document.getElementById('modalTitle');
        const formMethod = document.getElementById('formMethod');
        const passwordFields = document.getElementById('passwordFields');
        
        modalTitle.textContent = 'Editar Usuario';
        formMethod.value = 'PUT';
        // Cargar datos del usuario...
        passwordFields.style.display = 'none';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function deleteUser(name) {
        if (confirm(`¿Estás seguro de que deseas eliminar al usuario "${name}"?`)) {
            // Aquí enviarías la solicitud de eliminación
            alert('Usuario eliminado (demo)');
        }
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('userModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeUserModal();
        }
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