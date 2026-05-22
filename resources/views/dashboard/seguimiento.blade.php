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
        <button id="openTrackingModalBtn" class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus w-5 h-5"></i>
            Nuevo Seguimiento
        </button>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-700 mb-1">Al día</p>
                    <p class="text-2xl font-bold text-green-900">124</p>
                </div>
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-yellow-700 mb-1">Pendientes</p>
                    <p class="text-2xl font-bold text-yellow-900">38</p>
                </div>
                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-700 mb-1">Urgentes</p>
                    <p class="text-2xl font-bold text-red-900">12</p>
                </div>
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-700 mb-1">Controles Hoy</p>
                    <p class="text-2xl font-bold text-blue-900">18</p>
                </div>
                <i class="fas fa-calendar-day text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text"
                       id="searchInput"
                       placeholder="Buscar por paciente, enfermedad o médico..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los estados</option>
                    <option value="Al día">Al día</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Urgente">Urgente</option>
                </select>
                <select id="doctorFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los médicos</option>
                    <option value="Dr. Martínez">Dr. Martínez</option>
                    <option value="Dra. Rodríguez">Dra. Rodríguez</option>
                    <option value="Dr. Pérez">Dr. Pérez</option>
                </select>
            </div>
        </div>
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
                <tbody id="trackingTableBody" class="divide-y divide-gray-200">
                    @php
                        $seguimientos = [
                            ['patient' => 'Juan Pérez', 'patient_id' => 1, 'disease' => 'Diabetes Tipo 2', 'lastControl' => '2026-05-10', 'nextControl' => '2026-06-10', 'status' => 'Al día', 'statusColor' => 'green', 'doctor' => 'Dr. Martínez', 'notes' => 'Paciente estable, glucemia controlada', 'medications' => 'Metformina 850mg'],
                            ['patient' => 'María García', 'patient_id' => 2, 'disease' => 'Hipertensión', 'lastControl' => '2026-05-12', 'nextControl' => '2026-05-20', 'status' => 'Pendiente', 'statusColor' => 'yellow', 'doctor' => 'Dra. Rodríguez', 'notes' => 'Presión arterial elevada', 'medications' => 'Enalapril 10mg'],
                            ['patient' => 'Carlos López', 'patient_id' => 3, 'disease' => 'Asma Crónica', 'lastControl' => '2026-05-14', 'nextControl' => '2026-05-18', 'status' => 'Urgente', 'statusColor' => 'red', 'doctor' => 'Dr. Martínez', 'notes' => 'Crisis asmática recurrente', 'medications' => 'Salbutamol inhalador'],
                            ['patient' => 'Ana Martínez', 'patient_id' => 4, 'disease' => 'Obesidad', 'lastControl' => '2026-05-11', 'nextControl' => '2026-06-11', 'status' => 'Al día', 'statusColor' => 'green', 'doctor' => 'Dra. Rodríguez', 'notes' => 'Pérdida de peso progresiva', 'medications' => 'Plan nutricional'],
                            ['patient' => 'Pedro Rodríguez', 'patient_id' => 5, 'disease' => 'Artritis', 'lastControl' => '2026-05-13', 'nextControl' => '2026-05-25', 'status' => 'Pendiente', 'statusColor' => 'yellow', 'doctor' => 'Dr. Pérez', 'notes' => 'Dolor articular controlado', 'medications' => 'Ibuprofeno 400mg'],
                            ['patient' => 'Laura Fernández', 'patient_id' => 6, 'disease' => 'Diabetes Tipo 1', 'lastControl' => '2026-05-09', 'nextControl' => '2026-05-16', 'status' => 'Urgente', 'statusColor' => 'red', 'doctor' => 'Dr. Martínez', 'notes' => 'Descompensación glucémica', 'medications' => 'Insulina'],
                        ];
                    @endphp

                    @forelse($seguimientos as $seguimiento)
                        <tr class="hover:bg-gray-50 transition-colors tracking-row" 
                            data-patient="{{ strtolower($seguimiento['patient']) }}"
                            data-disease="{{ strtolower($seguimiento['disease']) }}"
                            data-doctor="{{ strtolower($seguimiento['doctor']) }}"
                            data-status="{{ $seguimiento['status'] }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user-circle text-blue-600 text-xl"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $seguimiento['patient'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-gray-700">{{ $seguimiento['disease'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-check text-gray-400 text-sm"></i>
                                    <span class="text-gray-700">{{ $seguimiento['lastControl'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-day text-gray-400 text-sm"></i>
                                    <span class="font-medium 
                                        {{ $seguimiento['status'] === 'Urgente' ? 'text-red-600' : 'text-gray-700' }}">
                                        {{ $seguimiento['nextControl'] }}
                                    </span>
                                    @php
                                        $nextDate = \Carbon\Carbon::parse($seguimiento['nextControl']);
                                        $daysLeft = \Carbon\Carbon::now()->diffInDays($nextDate, false);
                                    @endphp
                                    @if($daysLeft <= 3 && $daysLeft >= 0)
                                        <span class="text-xs text-orange-600 ml-1">({{ $daysLeft }} días)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $seguimiento['statusColor'] === 'green' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $seguimiento['statusColor'] === 'yellow' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $seguimiento['statusColor'] === 'red' ? 'bg-red-100 text-red-800' : '' }}">
                                    <i class="fas 
                                        {{ $seguimiento['status'] === 'Al día' ? 'fa-check-circle' : '' }}
                                        {{ $seguimiento['status'] === 'Pendiente' ? 'fa-clock' : '' }}
                                        {{ $seguimiento['status'] === 'Urgente' ? 'fa-exclamation-triangle' : '' }} 
                                        mr-1 text-xs"></i>
                                    {{ $seguimiento['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-purple-600 text-xs font-medium">
                                            {{ substr($seguimiento['doctor'], 4, 1) }}
                                        </span>
                                    </div>
                                    <span class="text-gray-700">{{ $seguimiento['doctor'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <button onclick="viewTracking({{ json_encode($seguimiento) }})" 
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                            title="Ver detalles">
                                        <i class="fas fa-eye text-blue-600 group-hover:text-blue-700"></i>
                                    </button>
                                    <button onclick="editTracking({{ $seguimiento['patient_id'] }})" 
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                            title="Editar seguimiento">
                                        <i class="fas fa-edit text-green-600 group-hover:text-green-700"></i>
                                    </button>
                                    <button onclick="addNote({{ $seguimiento['patient_id'] }})" 
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                            title="Agregar nota">
                                        <i class="fas fa-sticky-note text-purple-600 group-hover:text-purple-700"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-stethoscope text-4xl text-gray-300 mb-3 block"></i>
                                <p>No hay registros de seguimiento</p>
                                <button onclick="openTrackingModal()" class="mt-3 text-blue-600 hover:text-blue-700 font-medium">
                                    Crear el primer seguimiento
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
                <p class="text-sm text-gray-500">Mostrando 1-6 de 48 seguimientos</p>
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

<!-- Modal para Nuevo Seguimiento -->
<div id="trackingModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto transform transition-all">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Nuevo Seguimiento</h3>
            <button onclick="closeTrackingModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times w-6 h-6 text-xl"></i>
            </button>
        </div>

        <form id="trackingForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="tracking_id" id="trackingId">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
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
                    <label for="disease" class="block text-sm font-medium text-gray-700 mb-1">Enfermedad *</label>
                    <select id="disease" name="disease" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar enfermedad</option>
                        <option value="Diabetes Tipo 2">Diabetes Tipo 2</option>
                        <option value="Hipertensión">Hipertensión</option>
                        <option value="Asma Crónica">Asma Crónica</option>
                        <option value="Obesidad">Obesidad</option>
                        <option value="Artritis">Artritis</option>
                    </select>
                </div>

                <div>
                    <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-1">Médico Asignado *</label>
                    <select id="doctor_id" name="doctor_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar médico</option>
                        <option value="1">Dr. Carlos Martínez</option>
                        <option value="2">Dra. María Rodríguez</option>
                        <option value="3">Dr. Juan Pérez</option>
                    </select>
                </div>

                <div>
                    <label for="last_control" class="block text-sm font-medium text-gray-700 mb-1">Último Control *</label>
                    <input type="date" id="last_control" name="last_control" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="next_control" class="block text-sm font-medium text-gray-700 mb-1">Próximo Control *</label>
                    <input type="date" id="next_control" name="next_control" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="Al día">Al día</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Urgente">Urgente</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="medications" class="block text-sm font-medium text-gray-700 mb-1">Medicamentos</label>
                    <textarea id="medications" name="medications" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Medicamentos recetados..."></textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notas del Seguimiento</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Observaciones, síntomas, recomendaciones..."></textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="vital_signs" class="block text-sm font-medium text-gray-700 mb-1">Signos Vitales</label>
                    <div class="grid grid-cols-3 gap-3">
                        <input type="text" name="blood_pressure" placeholder="Presión arterial" 
                               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="text" name="heart_rate" placeholder="Frec. cardíaca"
                               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="text" name="glucose" placeholder="Glucosa"
                               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeTrackingModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i> Guardar Seguimiento
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Ver Detalles del Seguimiento -->
<div id="viewTrackingModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Detalles del Seguimiento</h3>
            <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times w-6 h-6 text-xl"></i>
            </button>
        </div>
        <div class="p-6" id="trackingDetails">
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
        const rows = document.querySelectorAll('.tracking-row');
        
        rows.forEach(row => {
            const patient = row.dataset.patient || '';
            const disease = row.dataset.disease || '';
            const doctor = row.dataset.doctor || '';
            const status = row.dataset.status || '';
            
            const matchesSearch = patient.includes(searchTerm) || disease.includes(searchTerm) || doctor.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;
            const matchesDoctor = !doctorFilter || doctor.includes(doctorFilter);
            
            row.style.display = (matchesSearch && matchesStatus && matchesDoctor) ? '' : 'none';
        });
    }
    
    document.getElementById('searchInput')?.addEventListener('keyup', filterTable);
    document.getElementById('statusFilter')?.addEventListener('change', filterTable);
    document.getElementById('doctorFilter')?.addEventListener('change', filterTable);
    
    function openTrackingModal() {
        const modal = document.getElementById('trackingModal');
        const modalTitle = document.getElementById('modalTitle');
        const formMethod = document.getElementById('formMethod');
        const trackingId = document.getElementById('trackingId');
        
        modalTitle.textContent = 'Nuevo Seguimiento';
        formMethod.value = 'POST';
        trackingId.value = '';
        document.getElementById('trackingForm').reset();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closeTrackingModal() {
        const modal = document.getElementById('trackingModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    function closeViewModal() {
        const modal = document.getElementById('viewTrackingModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    function viewTracking(tracking) {
        const modal = document.getElementById('viewTrackingModal');
        const details = document.getElementById('trackingDetails');
        
        const statusColors = {
            'Al día': 'bg-green-100 text-green-800',
            'Pendiente': 'bg-yellow-100 text-yellow-800',
            'Urgente': 'bg-red-100 text-red-800'
        };
        
        details.innerHTML = `
            <div class="space-y-6">
                <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-circle text-blue-600 text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">${tracking.patient}</h2>
                        <p class="text-gray-500">ID: PAC-00${tracking.patient_id}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">🏥 Enfermedad</p>
                        <p class="font-medium">${tracking.disease}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">👨‍⚕️ Médico</p>
                        <p class="font-medium">${tracking.doctor}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">📅 Último Control</p>
                        <p class="font-medium">${tracking.lastControl}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">📅 Próximo Control</p>
                        <p class="font-medium text-${tracking.status === 'Urgente' ? 'red' : 'gray'}-600">${tracking.nextControl}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">📊 Estado</p>
                        <p><span class="inline-flex px-2 py-1 text-xs font-medium rounded-full ${statusColors[tracking.status]}">${tracking.status}</span></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">💊 Medicamentos</p>
                        <p class="font-medium">${tracking.medications || 'No registrados'}</p>
                    </div>
                    <div class="md:col-span-2 bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">📝 Notas del Seguimiento</p>
                        <p class="text-gray-700">${tracking.notes || 'Sin observaciones'}</p>
                    </div>
                </div>
                
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-blue-600 mb-2">📌 Recomendaciones</p>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Mantener el control regular según lo programado</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Seguir las indicaciones médicas al pie de la letra</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Reportar cualquier síntoma nuevo o cambio en su condición</span>
                        </li>
                    </ul>
                </div>
            </div>
        `;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function editTracking(patientId) {
        openTrackingModal();
        document.getElementById('modalTitle').textContent = 'Editar Seguimiento';
        document.getElementById('formMethod').value = 'PUT';
        // Aquí cargarías los datos del seguimiento para editar
    }
    
    function addNote(patientId) {
        alert(`Agregar nota para el paciente ID: ${patientId}`);
        // Aquí abrirías un modal para agregar notas rápidas
    }
    
    // Cerrar modales al hacer clic fuera
    document.getElementById('trackingModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeTrackingModal();
    });
    document.getElementById('viewTrackingModal')?.addEventListener('click', function(e) {
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