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
            <button id="openPatientModalBtn"
                class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus w-5 h-5"></i>
                Registrar Paciente
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Pacientes</p>
                        <p class="text-3xl font-bold text-gray-900">1,234</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-xs text-green-600">
                    <i class="fas fa-arrow-up"></i>
                    <span>+12.5% vs mes anterior</span>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pacientes Activos</p>
                        <p class="text-3xl font-bold text-green-600">856</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-xs text-green-600">
                    <i class="fas fa-arrow-up"></i>
                    <span>+8.2% vs mes anterior</span>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pacientes Críticos</p>
                        <p class="text-3xl font-bold text-red-600">18</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-xs text-red-600">
                    <i class="fas fa-arrow-down"></i>
                    <span>-2.1% vs mes anterior</span>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <form action="{}" method="GET" class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" placeholder="Buscar pacientes por nombre, documento o enfermedad..."
                    value="{{ request('search') }}"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </form>
        </div>

        <!-- Filter Tabs -->
        <div class="flex flex-wrap gap-2 border-b border-gray-200">
            <button class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600">Todos</button>
            <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Activos</button>
            <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Críticos</button>
            <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">En Seguimiento</button>
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
                        @php
                            $pacientes = [
                                ['name' => 'Juan Pérez', 'document' => '1234567890', 'disease' => 'Diabetes Tipo 2', 'status' => 'Estable', 'statusColor' => 'green', 'lastControl' => '2026-05-10', 'phone' => '3001234567', 'age' => 45],
                                ['name' => 'María García', 'document' => '0987654321', 'disease' => 'Hipertensión', 'status' => 'Control', 'statusColor' => 'yellow', 'lastControl' => '2026-05-12', 'phone' => '3007654321', 'age' => 58],
                                ['name' => 'Carlos López', 'document' => '1122334455', 'disease' => 'Asma Crónica', 'status' => 'Crítico', 'statusColor' => 'red', 'lastControl' => '2026-05-14', 'phone' => '3012345678', 'age' => 32],
                                ['name' => 'Ana Martínez', 'document' => '5566778899', 'disease' => 'Obesidad', 'status' => 'Estable', 'statusColor' => 'green', 'lastControl' => '2026-05-11', 'phone' => '3023456789', 'age' => 41],
                                ['name' => 'Pedro Rodríguez', 'document' => '9988776655', 'disease' => 'Artritis', 'status' => 'Control', 'statusColor' => 'yellow', 'lastControl' => '2026-05-13', 'phone' => '3034567890', 'age' => 62],
                                ['name' => 'Laura Fernández', 'document' => '4433221100', 'disease' => 'Diabetes Tipo 1', 'status' => 'Crítico', 'statusColor' => 'red', 'lastControl' => '2026-05-15', 'phone' => '3045678901', 'age' => 28],
                            ];
                        @endphp

                        @forelse($pacientes as $paciente)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user-circle w-6 h-6 text-blue-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $paciente['name'] }}</span>
                                            <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5">
                                                <span>{{ $paciente['age'] }} años</span>
                                                <span>•</span>
                                                <span>{{ $paciente['phone'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $paciente['document'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-gray-700">{{ $paciente['disease'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                            {{ $paciente['statusColor'] === 'green' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $paciente['statusColor'] === 'yellow' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $paciente['statusColor'] === 'red' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $paciente['status'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $paciente['lastControl'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <button onclick="viewPatient('{{ $paciente['name'] }}')"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                            title="Ver detalles">
                                            <i class="fas fa-eye w-4 h-4 text-blue-600 group-hover:text-blue-700"></i>
                                        </button>
                                        <button onclick="editPatient('{{ $paciente['document'] }}')"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                            title="Editar paciente">
                                            <i class="fas fa-edit w-4 h-4 text-green-600 group-hover:text-green-700"></i>
                                        </button>
                                        <button onclick="showMedicalHistory('{{ $paciente['name'] }}')"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                            title="Historial clínico">
                                            <i class="fas fa-file-alt w-4 h-4 text-purple-600 group-hover:text-purple-700"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-users text-4xl text-gray-300 mb-3 block"></i>
                                    <p>No hay pacientes registrados</p>
                                    <button onclick="openPatientModal()"
                                        class="mt-3 text-blue-600 hover:text-blue-700 font-medium">
                                        Registrar el primer paciente
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-500">Mostrando 1-6 de 234 pacientes</p>
                    <div class="flex gap-2">
                        <button
                            class="px-3 py-1 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 disabled:opacity-50"
                            disabled>
                            <i class="fas fa-chevron-left text-xs"></i> Anterior
                        </button>
                        <button class="px-3 py-1 bg-blue-600 text-white rounded-lg">1</button>
                        <button
                            class="px-3 py-1 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">2</button>
                        <button
                            class="px-3 py-1 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">3</button>
                        <button
                            class="px-3 py-1 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">4</button>
                        <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
                            Siguiente <i class="fas fa-chevron-right text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Registrar/Editar Paciente -->
    <div id="patientModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Registrar Paciente</h3>
                <button onclick="closePatientModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>

            <form id="patientForm" method="POST" action="{}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="patient_id" id="patientId">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Información Personal</label>
                        <div class="h-px bg-gray-200 my-2"></div>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
                        <input type="text" id="name" name="name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="document" class="block text-sm font-medium text-gray-700 mb-1">Documento de Identidad
                            *</label>
                        <input type="text" id="document" name="document" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha de
                            Nacimiento</label>
                        <input type="date" id="birth_date" name="birth_date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="tel" id="phone" name="phone"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" id="email" name="email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                        <input type="text" id="address" name="address"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="md:col-span-2 mt-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Información Médica</label>
                        <div class="h-px bg-gray-200 my-2"></div>
                    </div>

                    <div>
                        <label for="disease" class="block text-sm font-medium text-gray-700 mb-1">Enfermedad / Diagnóstico
                            *</label>
                        <select id="disease" name="disease" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccionar enfermedad</option>
                            <option value="Diabetes Tipo 1">Diabetes Tipo 1</option>
                            <option value="Diabetes Tipo 2">Diabetes Tipo 2</option>
                            <option value="Hipertensión">Hipertensión</option>
                            <option value="Asma Crónica">Asma Crónica</option>
                            <option value="Obesidad">Obesidad</option>
                            <option value="Artritis">Artritis</option>
                            <option value="EPOC">EPOC</option>
                            <option value="Insuficiencia Renal">Insuficiencia Renal</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select id="status" name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="Estable">Estable</option>
                            <option value="Control">En Control</option>
                            <option value="Crítico">Crítico</option>
                        </select>
                    </div>

                    <div>
                        <label for="risk_level" class="block text-sm font-medium text-gray-700 mb-1">Nivel de Riesgo</label>
                        <select id="risk_level" name="risk_level"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="Bajo">Bajo</option>
                            <option value="Medio">Medio</option>
                            <option value="Alto">Alto</option>
                        </select>
                    </div>

                    <div>
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-1">Médico Asignado</label>
                        <select id="doctor_id" name="doctor_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccionar médico</option>
                            <option value="1">Dr. Carlos Martínez</option>
                            <option value="2">Dra. María Rodríguez</option>
                            <option value="3">Dr. Juan Pérez</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="observations" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea id="observations" name="observations" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Notas adicionales sobre el paciente..."></textarea>
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
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times w-6 h-6 text-xl"></i>
                </button>
            </div>
            <div class="p-6" id="patientDetails">
                <!-- Los detalles se cargarán dinámicamente -->
            </div>
        </div>
    </div>

    <script>
        function openPatientModal() {
            const modal = document.getElementById('patientModal');
            const modalTitle = document.getElementById('modalTitle');
            const formMethod = document.getElementById('formMethod');
            const patientId = document.getElementById('patientId');

            modalTitle.textContent = 'Registrar Paciente';
            formMethod.value = 'POST';
            patientId.value = '';
            document.getElementById('patientForm').reset();
            modal.classList.remove('hidden');
            modal.classList.add('flex');
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

        function viewPatient(name) {
            const modal = document.getElementById('viewPatientModal');
            const details = document.getElementById('patientDetails');

            // Aquí cargarías los detalles desde el backend
            details.innerHTML = `
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-circle text-blue-600 text-4xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">${name}</h2>
                        <p class="text-gray-500">ID: PAC-001234</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Documento</p>
                        <p class="font-medium">1234567890</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Teléfono</p>
                        <p class="font-medium">3001234567</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Correo Electrónico</p>
                        <p class="font-medium">juan@email.com</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Dirección</p>
                        <p class="font-medium">Calle Principal #123</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Enfermedad</p>
                        <p class="font-medium">Diabetes Tipo 2</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Estado</p>
                        <p class="font-medium"><span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Estable</span></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Nivel de Riesgo</p>
                        <p class="font-medium">Bajo</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Médico Asignado</p>
                        <p class="font-medium">Dr. Carlos Martínez</p>
                    </div>
                    <div class="md:col-span-2 bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Observaciones</p>
                        <p class="font-medium">Paciente con control regular de glucosa.</p>
                    </div>
                </div>
            `;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function editPatient(document) {
            openPatientModal();
            // Aquí cargarías los datos del paciente para editar
            document.getElementById('modalTitle').textContent = 'Editar Paciente';
            document.getElementById('formMethod').value = 'PUT';
        }

        function showMedicalHistory(name) {
            alert(`Ver historial clínico de: ${name}`);
            // Aquí redirigirías a la página de historial
            // window.location.href = `/dashboard/historial?patient=${name}`;
        }

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