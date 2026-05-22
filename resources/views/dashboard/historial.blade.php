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
            <button onclick="exportHistory()" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-download w-5 h-5"></i>
                Exportar Todo
            </button>
            <button onclick="printHistory()" class="flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
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
                    <p class="text-2xl font-bold text-blue-900">1,248</p>
                </div>
                <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
            </div>
            <p class="text-xs text-blue-600 mt-2">+156 este mes</p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-700 mb-1">Pacientes Atendidos</p>
                    <p class="text-2xl font-bold text-green-900">856</p>
                </div>
                <i class="fas fa-users text-green-600 text-2xl"></i>
            </div>
            <p class="text-xs text-green-600 mt-2">Activos en seguimiento</p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-700 mb-1">Médicos Activos</p>
                    <p class="text-2xl font-bold text-purple-900">12</p>
                </div>
                <i class="fas fa-user-md text-purple-600 text-2xl"></i>
            </div>
            <p class="text-xs text-purple-600 mt-2">En diferentes especialidades</p>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-orange-700 mb-1">Registros Hoy</p>
                    <p class="text-2xl font-bold text-orange-900">24</p>
                </div>
                <i class="fas fa-calendar-day text-orange-600 text-2xl"></i>
            </div>
            <p class="text-xs text-orange-600 mt-2">+8 vs ayer</p>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text"
                       id="searchInput"
                       placeholder="Buscar por paciente, diagnóstico o médico..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex flex-wrap gap-3">
                <select id="dateFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los períodos</option>
                    <option value="today">Hoy</option>
                    <option value="week">Esta semana</option>
                    <option value="month">Este mes</option>
                    <option value="year">Este año</option>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especialidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody" class="divide-y divide-gray-200">
                    @php
                        $historial = [
                            ['date' => '2026-05-14', 'patient' => 'Juan Pérez', 'patient_id' => 1, 'diagnosis' => 'Control de glucosa - Diabetes Tipo 2', 'treatment' => 'Metformina 850mg cada 12 horas', 'doctor' => 'Dr. Martínez', 'specialty' => 'Endocrinología', 'notes' => 'Paciente estable, glucemia en rangos normales', 'medications' => 'Metformina, Losartán'],
                            ['date' => '2026-05-13', 'patient' => 'María García', 'patient_id' => 2, 'diagnosis' => 'Control de presión arterial - Hipertensión', 'treatment' => 'Losartán 50mg diario', 'doctor' => 'Dra. Rodríguez', 'specialty' => 'Cardiología', 'notes' => 'Presión arterial controlada', 'medications' => 'Losartán'],
                            ['date' => '2026-05-12', 'patient' => 'Carlos López', 'patient_id' => 3, 'diagnosis' => 'Crisis asmática leve - Asma Crónica', 'treatment' => 'Salbutamol inhalador según necesidad', 'doctor' => 'Dr. Martínez', 'specialty' => 'Neumología', 'notes' => 'Mejoría con el tratamiento', 'medications' => 'Salbutamol, Budesonida'],
                            ['date' => '2026-05-11', 'patient' => 'Ana Martínez', 'patient_id' => 4, 'diagnosis' => 'Evaluación de peso - Obesidad', 'treatment' => 'Plan nutricional y ejercicio', 'doctor' => 'Dra. Gómez', 'specialty' => 'Nutrición', 'notes' => 'Pérdida de 2kg en el último mes', 'medications' => 'Sin medicamentos'],
                            ['date' => '2026-05-10', 'patient' => 'Pedro Rodríguez', 'patient_id' => 5, 'diagnosis' => 'Control de artritis', 'treatment' => 'Ibuprofeno 400mg cada 8 horas', 'doctor' => 'Dr. Pérez', 'specialty' => 'Reumatología', 'notes' => 'Dolor controlado', 'medications' => 'Ibuprofeno'],
                            ['date' => '2026-05-09', 'patient' => 'Laura Fernández', 'patient_id' => 6, 'diagnosis' => 'Control de glucosa - Diabetes Tipo 1', 'treatment' => 'Insulina basal 10 UI', 'doctor' => 'Dr. Martínez', 'specialty' => 'Endocrinología', 'notes' => 'Requiere ajuste de dosis', 'medications' => 'Insulina Glargina'],
                        ];
                    @endphp

                    @forelse($historial as $record)
                        <tr class="hover:bg-gray-50 transition-colors history-row" 
                            data-date="{{ $record['date'] }}"
                            data-patient="{{ strtolower($record['patient']) }}"
                            data-doctor="{{ strtolower($record['doctor']) }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                                    <span class="text-gray-700">{{ \Carbon\Carbon::parse($record['date'])->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $record['patient'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs">
                                    <p class="text-sm text-gray-900 font-medium">{{ explode(' - ', $record['diagnosis'])[0] }}</p>
                                    <p class="text-xs text-gray-500">{{ explode(' - ', $record['diagnosis'])[1] ?? '' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 max-w-xs">{{ $record['treatment'] }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-purple-600 text-xs font-medium">
                                            {{ substr($record['doctor'], 4, 1) }}
                                        </span>
                                    </div>
                                    <span class="text-gray-700">{{ $record['doctor'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                    {{ $record['specialty'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <button onclick="viewHistory({{ json_encode($record) }})" 
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                            title="Ver detalles">
                                        <i class="fas fa-eye text-blue-600 group-hover:text-blue-700"></i>
                                    </button>
                                    <button onclick="downloadRecord({{ $record['patient_id'] }})" 
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                            title="Descargar PDF">
                                        <i class="fas fa-download text-green-600 group-hover:text-green-700"></i>
                                    </button>
                                    <button onclick="shareRecord({{ $record['patient_id'] }})" 
                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors group"
                                            title="Compartir">
                                        <i class="fas fa-share-alt text-purple-600 group-hover:text-purple-700"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-folder-open text-4xl text-gray-300 mb-3 block"></i>
                                <p>No hay registros en el historial clínico</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-500">Mostrando 1-6 de 1,248 registros</p>
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
            <!-- Los detalles se cargarán dinámicamente -->
        </div>
    </div>
</div>

<script>
    // Filtros de búsqueda
    function filterTable() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const dateFilter = document.getElementById('dateFilter').value;
        const doctorFilter = document.getElementById('doctorFilter').value.toLowerCase();
        const rows = document.querySelectorAll('.history-row');
        
        rows.forEach(row => {
            const patient = row.dataset.patient || '';
            const doctor = row.dataset.doctor || '';
            const date = row.dataset.date || '';
            
            let matchesSearch = patient.includes(searchTerm);
            let matchesDoctor = !doctorFilter || doctor.includes(doctorFilter);
            let matchesDate = true;
            
            // Filtro de fecha
            if (dateFilter) {
                const today = new Date().toISOString().split('T')[0];
                const rowDate = date;
                
                if (dateFilter === 'today') {
                    matchesDate = rowDate === today;
                } else if (dateFilter === 'week') {
                    const weekAgo = new Date();
                    weekAgo.setDate(weekAgo.getDate() - 7);
                    matchesDate = rowDate >= weekAgo.toISOString().split('T')[0];
                } else if (dateFilter === 'month') {
                    const monthAgo = new Date();
                    monthAgo.setMonth(monthAgo.getMonth() - 1);
                    matchesDate = rowDate >= monthAgo.toISOString().split('T')[0];
                }
            }
            
            row.style.display = (matchesSearch && matchesDoctor && matchesDate) ? '' : 'none';
        });
    }
    
    document.getElementById('searchInput')?.addEventListener('keyup', filterTable);
    document.getElementById('dateFilter')?.addEventListener('change', filterTable);
    document.getElementById('doctorFilter')?.addEventListener('change', filterTable);
    
    document.getElementById('resetFilters')?.addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('dateFilter').value = '';
        document.getElementById('doctorFilter').value = '';
        filterTable();
    });
    
    function closeViewModal() {
        const modal = document.getElementById('viewHistoryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    function viewHistory(record) {
        const modal = document.getElementById('viewHistoryModal');
        const details = document.getElementById('historyDetails');
        
        details.innerHTML = `
            <div class="space-y-6">
                <!-- Cabecera -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-medical text-blue-600 text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">${record.patient}</h2>
                            <p class="text-gray-500">ID: PAC-00${record.patient_id} | Fecha: ${record.date}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="downloadRecord(${record.patient_id})" class="px-3 py-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                            <i class="fas fa-download"></i> PDF
                        </button>
                        <button onclick="window.print()" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
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
                            <p class="font-semibold text-gray-900">${record.doctor}</p>
                            <p class="text-sm text-gray-600">${record.specialty}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Diagnóstico y tratamiento -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-blue-600 mb-2">📋 Diagnóstico</p>
                        <p class="text-gray-800">${record.diagnosis}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-sm text-green-600 mb-2">💊 Tratamiento</p>
                        <p class="text-gray-800">${record.treatment}</p>
                    </div>
                </div>
                
                <!-- Medicación -->
                <div class="bg-yellow-50 rounded-lg p-4">
                    <p class="text-sm text-yellow-600 mb-2">💊 Medicación actual</p>
                    <div class="flex flex-wrap gap-2">
                        ${record.medications.split(',').map(med => 
                            `<span class="px-3 py-1 bg-white rounded-full text-sm text-gray-700 border border-gray-200">${med.trim()}</span>`
                        ).join('')}
                    </div>
                </div>
                
                <!-- Notas adicionales -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-2">📝 Notas adicionales</p>
                    <p class="text-gray-700">${record.notes || 'Sin observaciones registradas'}</p>
                </div>
                
                <!-- Signos vitales (ejemplo) -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Presión Arterial</p>
                        <p class="font-semibold text-gray-900">120/80</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Frec. Cardíaca</p>
                        <p class="font-semibold text-gray-900">72 lpm</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Glucosa</p>
                        <p class="font-semibold text-gray-900">110 mg/dL</p>
                    </div>
                </div>
                
                <!-- Próxima cita -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-blue-600 mb-2">📅 Próxima cita programada</p>
                    <p class="text-gray-800">${new Date(new Date(record.date).setMonth(new Date(record.date).getMonth() + 1)).toLocaleDateString('es-CO')}</p>
                </div>
            </div>
        `;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function downloadRecord(patientId) {
        alert(`Descargando historial clínico del paciente ID: ${patientId}`);
        // Aquí implementarías la generación de PDF
    }
    
    function shareRecord(patientId) {
        alert(`Compartir historial del paciente ID: ${patientId}`);
        // Aquí implementarías la funcionalidad de compartir
    }
    
    function exportHistory() {
        alert('Exportando todo el historial clínico...');
        // Aquí implementarías la exportación a Excel/PDF
    }
    
    function printHistory() {
        window.print();
    }
    
    // Cerrar modal al hacer clic fuera
    document.getElementById('viewHistoryModal')?.addEventListener('click', function(e) {
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
        .fixed, .sticky, header, .bg-gray-50, button:not(.no-print) {
            display: none !important;
        }
        body {
            background: white;
        }
        .rounded-xl, .border {
            border: 1px solid #e5e7eb !important;
        }
    }
</style>
@endsection