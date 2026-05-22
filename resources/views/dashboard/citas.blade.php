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
        <button id="openAppointmentModalBtn" class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus w-5 h-5"></i>
            Nueva Cita
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Citas Hoy</p>
                    <p class="text-3xl font-bold text-gray-900">42</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-green-600 mt-2">+8 vs ayer</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pendientes</p>
                    <p class="text-3xl font-bold text-orange-600">15</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-lg">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-orange-600 mt-2">Requieren atención</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Completadas</p>
                    <p class="text-3xl font-bold text-green-600">27</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-green-600 mt-2">+5 vs ayer</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text"
                       id="searchInput"
                       placeholder="Buscar por paciente, médico o motivo..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex gap-3">
                <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Confirmada">Confirmada</option>
                    <option value="Completada">Completada</option>
                    <option value="Cancelada">Cancelada</option>
                </select>
                <select id="doctorFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los médicos</option>
                    <option value="Dr. Martínez">Dr. Carlos Martínez</option>
                    <option value="Dra. Rodríguez">Dra. María Rodríguez</option>
                    <option value="Dr. Pérez">Dr. Juan Pérez</option>
                    <option value="Dra. Gómez">Dra. Ana Gómez</option>
                </select>
                <button id="resetFilters" class="px-4 py-2 text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-undo-alt mr-1"></i> Limpiar
                </button>
            </div>
        </div>
    </div>

    <!-- Today's Schedule Highlight -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h3 class="text-lg font-semibold mb-1">Agenda de Hoy</h3>
                <p class="text-blue-100">{{ date('d/m/Y') }}</p>
            </div>
            <div class="flex gap-6">
                <div class="text-center">
                    <p class="text-2xl font-bold">8</p>
                    <p class="text-xs text-blue-100">Completadas</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold">5</p>
                    <p class="text-xs text-blue-100">Pendientes</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold">2</p>
                    <p class="text-xs text-blue-100">En curso</p>
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
                <tbody id="appointmentsTableBody" class="divide-y divide-gray-200">
                    @php
                        $citas = [
                            ['date' => '2026-05-15', 'time' => '09:00 AM', 'patient' => 'Juan Pérez', 'patient_id' => 1, 'reason' => 'Control de glucosa', 'doctor' => 'Dr. Martínez', 'specialty' => 'Endocrinología', 'status' => 'Pendiente', 'statusColor' => 'orange', 'phone' => '3001234567'],
                            ['date' => '2026-05-15', 'time' => '10:30 AM', 'patient' => 'María García', 'patient_id' => 2, 'reason' => 'Control de presión arterial', 'doctor' => 'Dra. Rodríguez', 'specialty' => 'Cardiología', 'status' => 'Confirmada', 'statusColor' => 'blue', 'phone' => '3007654321'],
                            ['date' => '2026-05-15', 'time' => '02:00 PM', 'patient' => 'Carlos López', 'patient_id' => 3, 'reason' => 'Seguimiento asma', 'doctor' => 'Dr. Martínez', 'specialty' => 'Neumología', 'status' => 'Completada', 'statusColor' => 'green', 'phone' => '3012345678'],
                            ['date' => '2026-05-16', 'time' => '08:30 AM', 'patient' => 'Ana Martínez', 'patient_id' => 4, 'reason' => 'Control de peso', 'doctor' => 'Dra. Gómez', 'specialty' => 'Nutrición', 'status' => 'Confirmada', 'statusColor' => 'blue', 'phone' => '3023456789'],
                            ['date' => '2026-05-16', 'time' => '11:00 AM', 'patient' => 'Pedro Rodríguez', 'patient_id' => 5, 'reason' => 'Dolor articular', 'doctor' => 'Dr. Pérez', 'specialty' => 'Reumatología', 'status' => 'Pendiente', 'statusColor' => 'orange', 'phone' => '3034567890'],
                            ['date' => '2026-05-16', 'time' => '03:30 PM', 'patient' => 'Laura Fernández', 'patient_id' => 6, 'reason' => 'Control diabetes', 'doctor' => 'Dr. Martínez', 'specialty' => 'Endocrinología', 'status' => 'Cancelada', 'statusColor' => 'red', 'phone' => '3045678901'],
                        ];
                    @endphp

                    @forelse($citas as $cita)
                        <tr class="hover:bg-gray-50 transition-colors appointment-row" 
                            data-patient="{{ strtolower($cita['patient']) }}"
                            data-doctor="{{ strtolower($cita['doctor']) }}"
                            data-reason="{{ strtolower($cita['reason']) }}"
                            data-status="{{ $cita['status'] }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                                    <span class="text-gray-700">{{ \Carbon\Carbon::parse($cita['date'])->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock text-gray-400 text-sm"></i>
                                    <span class="font-medium text-gray-700">{{ $cita['time'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-900">{{ $cita['patient'] }}</span>
                                        <p class="text-xs text-gray-500">{{ $cita['phone'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700">{{ $cita['reason'] }}</p>
                                <p class="text-xs text-gray-500">{{ $cita['specialty'] }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-purple-600 text-xs font-medium">
                                            {{ substr($cita['doctor'], 4, 1) }}
                                        </span>
                                    </div>
                                    <span class="text-gray-700">{{ $cita['doctor'] }}</span>
                                </div>
                             </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $cita['statusColor'] === 'green' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $cita['statusColor'] === 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $cita['statusColor'] === 'orange' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $cita['statusColor'] === 'red' ? 'bg-red-100 text-red-800' : '' }}">
                                    <i class="fas 
                                        {{ $cita['status'] === 'Completada' ? 'fa-check-circle' : '' }}
                                        {{ $cita['status'] === 'Confirmada' ? 'fa-check' : '' }}
                                        {{ $cita['status'] === 'Pendiente' ? 'fa-clock' : '' }}
                                        {{ $cita['status'] === 'Cancelada' ? 'fa-times-circle' : '' }} 
                                        text-xs"></i>
                                    {{ $cita['status'] }}
                                </span>
                             </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <button onclick="viewAppointment({{ json_encode($cita) }})" 
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                            title="Ver detalles">
                                        <i class="fas fa-eye text-blue-600 group-hover:text-blue-700"></i>
                                    </button>
                                    <button onclick="editAppointment({{ $cita['patient_id'] }})" 
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                            title="Editar cita">
                                        <i class="fas fa-edit text-green-600 group-hover:text-green-700"></i>
                                    </button>
                                    <button onclick="cancelAppointment({{ $cita['patient_id'] }})" 
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                            title="Cancelar cita">
                                        <i class="fas fa-times text-red-600 group-hover:text-red-700"></i>
                                    </button>
                                </div>
                             </td>
                         </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3 block"></i>
                                <p>No hay citas programadas</p>
                                <button onclick="openAppointmentModal()" class="mt-3 text-blue-600 hover:text-blue-700 font-medium">
                                    Programar primera cita
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
                <p class="text-sm text-gray-500">Mostrando 1-6 de 48 citas</p>
                <div class="flex gap-2">
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 disabled:opacity-50" disabled>
                        <i class="fas fa-chevron-left text-xs"></i> Anterior
                    </button>
                    <button class="px-3 py-1 bg-blue-600 text-white rounded-lg">1</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">2</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">3</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
                        Siguiente <i class="fas fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Nueva/Editar Cita -->
<div id="appointmentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto transform transition-all">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Nueva Cita</h3>
            <button onclick="closeAppointmentModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times w-6 h-6 text-xl"></i>
            </button>
        </div>

        <form id="appointmentForm" class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-1">Paciente *</label>
                    <select id="patient_id" name="patient_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar paciente</option>
                        <option value="1">Juan Pérez</option>
                        <option value="2">María García</option>
                        <option value="3">Carlos López</option>
                        <option value="4">Ana Martínez</option>
                        <option value="5">Pedro Rodríguez</option>
                    </select>
                </div>

                <div>
                    <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-1">Médico *</label>
                    <select id="doctor_id" name="doctor_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar médico</option>
                        <option value="1">Dr. Carlos Martínez</option>
                        <option value="2">Dra. María Rodríguez</option>
                        <option value="3">Dr. Juan Pérez</option>
                        <option value="4">Dra. Ana Gómez</option>
                    </select>
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                    <input type="date" id="date" name="date" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="time" class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                    <select id="time" name="time" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar hora</option>
                        <option value="08:00 AM">08:00 AM</option>
                        <option value="08:30 AM">08:30 AM</option>
                        <option value="09:00 AM">09:00 AM</option>
                        <option value="09:30 AM">09:30 AM</option>
                        <option value="10:00 AM">10:00 AM</option>
                        <option value="10:30 AM">10:30 AM</option>
                        <option value="11:00 AM">11:00 AM</option>
                        <option value="11:30 AM">11:30 AM</option>
                        <option value="02:00 PM">02:00 PM</option>
                        <option value="02:30 PM">02:30 PM</option>
                        <option value="03:00 PM">03:00 PM</option>
                        <option value="03:30 PM">03:30 PM</option>
                        <option value="04:00 PM">04:00 PM</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Motivo de la consulta *</label>
                    <textarea id="reason" name="reason" rows="2" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Describa el motivo de la cita..."></textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notas adicionales</label>
                    <textarea id="notes" name="notes" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Instrucciones para el paciente, preparación previa..."></textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeAppointmentModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i> Programar Cita
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Ver Detalles de la Cita -->
<div id="viewAppointmentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Detalles de la Cita</h3>
            <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times w-6 h-6 text-xl"></i>
            </button>
        </div>
        <div class="p-6" id="appointmentDetails">
            <!-- Los detalles se cargarán dinámicamente -->
        </div>
    </div>
</div>

<script>
    // Filtros de búsqueda
    function filterTable() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const doctorFilter = document.getElementById('doctorFilter').value.toLowerCase();
        const rows = document.querySelectorAll('.appointment-row');
        
        rows.forEach(row => {
            const patient = row.dataset.patient || '';
            const doctor = row.dataset.doctor || '';
            const reason = row.dataset.reason || '';
            const status = row.dataset.status || '';
            
            const matchesSearch = patient.includes(searchTerm) || doctor.includes(searchTerm) || reason.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;
            const matchesDoctor = !doctorFilter || doctor.includes(doctorFilter);
            
            row.style.display = (matchesSearch && matchesStatus && matchesDoctor) ? '' : 'none';
        });
    }
    
    document.getElementById('searchInput')?.addEventListener('keyup', filterTable);
    document.getElementById('statusFilter')?.addEventListener('change', filterTable);
    document.getElementById('doctorFilter')?.addEventListener('change', filterTable);
    
    document.getElementById('resetFilters')?.addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('doctorFilter').value = '';
        filterTable();
    });
    
    function openAppointmentModal() {
        const modal = document.getElementById('appointmentModal');
        const modalTitle = document.getElementById('modalTitle');
        
        modalTitle.textContent = 'Nueva Cita';
        document.getElementById('appointmentForm').reset();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closeAppointmentModal() {
        const modal = document.getElementById('appointmentModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    function closeViewModal() {
        const modal = document.getElementById('viewAppointmentModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    function viewAppointment(appointment) {
        const modal = document.getElementById('viewAppointmentModal');
        const details = document.getElementById('appointmentDetails');
        
        const statusColors = {
            'Completada': 'bg-green-100 text-green-800',
            'Confirmada': 'bg-blue-100 text-blue-800',
            'Pendiente': 'bg-orange-100 text-orange-800',
            'Cancelada': 'bg-red-100 text-red-800'
        };
        
        details.innerHTML = `
            <div class="space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-check text-blue-600 text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">${appointment.patient}</h2>
                            <p class="text-gray-500">ID: PAC-00${appointment.patient_id}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium rounded-full ${statusColors[appointment.status]}">
                        ${appointment.status}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-blue-600 mb-1">📅 Fecha</p>
                        <p class="font-medium text-gray-900">${appointment.date}</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-blue-600 mb-1">⏰ Hora</p>
                        <p class="font-medium text-gray-900">${appointment.time}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-sm text-purple-600 mb-1">👨‍⚕️ Médico</p>
                        <p class="font-medium text-gray-900">${appointment.doctor}</p>
                        <p class="text-sm text-gray-600">${appointment.specialty}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-sm text-purple-600 mb-1">📞 Contacto</p>
                        <p class="font-medium text-gray-900">${appointment.phone}</p>
                    </div>
                    <div class="md:col-span-2 bg-green-50 rounded-lg p-4">
                        <p class="text-sm text-green-600 mb-1">📋 Motivo de la consulta</p>
                        <p class="text-gray-800">${appointment.reason}</p>
                    </div>
                </div>
                
                <div class="bg-yellow-50 rounded-lg p-4">
                    <p class="text-sm text-yellow-600 mb-2">📌 Recordatorio para el paciente</p>
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
                
                <div class="flex gap-3">
                    <button onclick="editAppointment(${appointment.patient_id})" 
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i> Editar
                    </button>
                    <button onclick="cancelAppointment(${appointment.patient_id})" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </button>
                </div>
            </div>
        `;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function editAppointment(patientId) {
        closeViewModal();
        openAppointmentModal();
        document.getElementById('modalTitle').textContent = 'Editar Cita';
        // Aquí cargarías los datos de la cita para editar
    }
    
    function cancelAppointment(patientId) {
        if (confirm('¿Estás seguro de que deseas cancelar esta cita?')) {
            alert(`Cita cancelada para el paciente ID: ${patientId}`);
            // Aquí implementarías la cancelación
        }
    }
    
    // Cerrar modales al hacer clic fuera
    document.getElementById('appointmentModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeAppointmentModal();
    });
    document.getElementById('viewAppointmentModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeViewModal();
    });
    
    // Submit del formulario
    document.getElementById('appointmentForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Cita programada exitosamente (demo)');
        closeAppointmentModal();
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