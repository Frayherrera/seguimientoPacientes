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
        <button id="openDiseaseModalBtn" class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus w-5 h-5"></i>
            Nueva Enfermedad
        </button>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form action="{}" method="GET" class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text"
                   name="search"
                   placeholder="Buscar enfermedades..."
                   value="{{ request('search') }}"
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </form>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4">
            <p class="text-sm text-blue-700 mb-1">Total Enfermedades</p>
            <p class="text-2xl font-bold text-blue-900">12</p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4">
            <p class="text-sm text-green-700 mb-1">Categorías</p>
            <p class="text-2xl font-bold text-green-900">6</p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4">
            <p class="text-sm text-purple-700 mb-1">Total Pacientes</p>
            <p class="text-2xl font-bold text-purple-900">1,135</p>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4">
            <p class="text-sm text-orange-700 mb-1">Más Común</p>
            <p class="text-lg font-bold text-orange-900">Diabetes Tipo 2</p>
        </div>
    </div>

    <!-- Diseases Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $enfermedades = [
                ['name' => 'Diabetes Tipo 2', 'patients' => 320, 'category' => 'Metabólica', 'categoryColor' => 'purple', 'color' => 'blue', 'description' => 'Enfermedad metabólica caracterizada por niveles altos de glucosa en sangre debido a resistencia a la insulina.', 'symptoms' => 'Sed excesiva, micción frecuente, fatiga, visión borrosa'],
                ['name' => 'Hipertensión', 'patients' => 280, 'category' => 'Cardiovascular', 'categoryColor' => 'red', 'color' => 'red', 'description' => 'Presión arterial elevada de forma crónica que puede causar problemas cardiovasculares.', 'symptoms' => 'Generalmente asintomática, dolores de cabeza, mareos'],
                ['name' => 'Asma Crónica', 'patients' => 150, 'category' => 'Respiratoria', 'categoryColor' => 'green', 'color' => 'green', 'description' => 'Enfermedad inflamatoria crónica de las vías respiratorias que causa dificultad para respirar.', 'symptoms' => 'Sibilancias, falta de aire, opresión en el pecho, tos'],
                ['name' => 'Obesidad', 'patients' => 200, 'category' => 'Metabólica', 'categoryColor' => 'purple', 'color' => 'orange', 'description' => 'Acumulación excesiva de grasa corporal que puede afectar la salud.', 'symptoms' => 'IMC elevado, dificultad para realizar actividades físicas'],
                ['name' => 'Artritis Reumatoide', 'patients' => 100, 'category' => 'Autoinmune', 'categoryColor' => 'pink', 'color' => 'purple', 'description' => 'Enfermedad autoinmune que causa inflamación crónica de las articulaciones.', 'symptoms' => 'Dolor articular, rigidez matutina, inflamación'],
                ['name' => 'Insuficiencia Renal', 'patients' => 85, 'category' => 'Renal', 'categoryColor' => 'indigo', 'color' => 'indigo', 'description' => 'Pérdida gradual de la función de los riñones para filtrar desechos de la sangre.', 'symptoms' => 'Fatiga, hinchazón, cambios en la micción, náuseas'],
                ['name' => 'EPOC', 'patients' => 65, 'category' => 'Respiratoria', 'categoryColor' => 'green', 'color' => 'teal', 'description' => 'Enfermedad pulmonar obstructiva crónica que dificulta la respiración.', 'symptoms' => 'Falta de aire, tos crónica, producción de esputo'],
                ['name' => 'Alzheimer', 'patients' => 45, 'category' => 'Neurológica', 'categoryColor' => 'gray', 'color' => 'gray', 'description' => 'Enfermedad neurodegenerativa que causa demencia progresiva.', 'symptoms' => 'Pérdida de memoria, confusión, cambios de comportamiento'],
            ];
        @endphp

        @forelse($enfermedades as $enfermedad)
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all group hover:-translate-y-1">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 rounded-lg 
                        {{ $enfermedad['color'] === 'blue' ? 'bg-blue-100 text-blue-600' : '' }}
                        {{ $enfermedad['color'] === 'red' ? 'bg-red-100 text-red-600' : '' }}
                        {{ $enfermedad['color'] === 'green' ? 'bg-green-100 text-green-600' : '' }}
                        {{ $enfermedad['color'] === 'orange' ? 'bg-orange-100 text-orange-600' : '' }}
                        {{ $enfermedad['color'] === 'purple' ? 'bg-purple-100 text-purple-600' : '' }}
                        {{ $enfermedad['color'] === 'indigo' ? 'bg-indigo-100 text-indigo-600' : '' }}
                        {{ $enfermedad['color'] === 'teal' ? 'bg-teal-100 text-teal-600' : '' }}
                        {{ $enfermedad['color'] === 'gray' ? 'bg-gray-100 text-gray-600' : '' }}">
                        <i class="fas fa-heartbeat text-xl"></i>
                    </div>
                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                        {{ $enfermedad['categoryColor'] === 'purple' ? 'bg-purple-100 text-purple-700' : '' }}
                        {{ $enfermedad['categoryColor'] === 'red' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $enfermedad['categoryColor'] === 'green' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $enfermedad['categoryColor'] === 'pink' ? 'bg-pink-100 text-pink-700' : '' }}
                        {{ $enfermedad['categoryColor'] === 'indigo' ? 'bg-indigo-100 text-indigo-700' : '' }}
                        {{ $enfermedad['categoryColor'] === 'gray' ? 'bg-gray-100 text-gray-700' : '' }}">
                        {{ $enfermedad['category'] }}
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $enfermedad['name'] }}</h3>
                <div class="flex items-center gap-2 text-gray-600 mb-3">
                    <i class="fas fa-chart-bar w-4 h-4"></i>
                    <span class="text-sm">{{ number_format($enfermedad['patients']) }} pacientes registrados</span>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <button onclick="viewDisease('{{ $enfermedad['name'] }}')" 
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 group">
                        Ver detalles
                        <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-white rounded-xl border border-gray-200">
                <i class="fas fa-activity text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">No hay enfermedades registradas</p>
                <button onclick="openDiseaseModal()" class="mt-3 text-blue-600 hover:text-blue-700 font-medium">
                    Crear la primera enfermedad
                </button>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
  
</div>

<!-- Modal para Registrar/Editar Enfermedad -->
<div id="diseaseModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto transform transition-all">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Nueva Enfermedad</h3>
            <button onclick="closeDiseaseModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times w-6 h-6 text-xl"></i>
            </button>
        </div>

        <form id="diseaseForm" method="POST" action="{}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="disease_id" id="diseaseId">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Enfermedad *</label>
                    <input type="text" id="name" name="name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ej: Diabetes Tipo 2">
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                    <select id="category" name="category" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar categoría</option>
                        <option value="Metabólica">Metabólica</option>
                        <option value="Cardiovascular">Cardiovascular</option>
                        <option value="Respiratoria">Respiratoria</option>
                        <option value="Autoinmune">Autoinmune</option>
                        <option value="Renal">Renal</option>
                        <option value="Neurológica">Neurológica</option>
                        <option value="Oncológica">Oncológica</option>
                        <option value="Digestiva">Digestiva</option>
                    </select>
                </div>

                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color de Identificación</label>
                    <select id="color" name="color"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="blue">Azul</option>
                        <option value="red">Rojo</option>
                        <option value="green">Verde</option>
                        <option value="orange">Naranja</option>
                        <option value="purple">Morado</option>
                        <option value="indigo">Índigo</option>
                        <option value="teal">Verde azulado</option>
                        <option value="pink">Rosa</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Descripción detallada de la enfermedad..."></textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-1">Síntomas Comunes</label>
                    <textarea id="symptoms" name="symptoms" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Lista de síntomas separados por comas..."></textarea>
                </div>

                <div>
                    <label for="risk_factors" class="block text-sm font-medium text-gray-700 mb-1">Factores de Riesgo</label>
                    <textarea id="risk_factors" name="risk_factors" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Factores de riesgo asociados..."></textarea>
                </div>

                <div>
                    <label for="treatment" class="block text-sm font-medium text-gray-700 mb-1">Tratamiento Recomendado</label>
                    <textarea id="treatment" name="treatment" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Opciones de tratamiento..."></textarea>
                </div>
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
            <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times w-6 h-6 text-xl"></i>
            </button>
        </div>
        <div class="p-6" id="diseaseDetails">
            <!-- Los detalles se cargarán dinámicamente -->
        </div>
    </div>
</div>

<script>
    function openDiseaseModal() {
        const modal = document.getElementById('diseaseModal');
        const modalTitle = document.getElementById('modalTitle');
        const formMethod = document.getElementById('formMethod');
        const diseaseId = document.getElementById('diseaseId');
        
        modalTitle.textContent = 'Nueva Enfermedad';
        formMethod.value = 'POST';
        diseaseId.value = '';
        document.getElementById('diseaseForm').reset();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
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

    function viewDisease(name) {
        const modal = document.getElementById('viewDiseaseModal');
        const details = document.getElementById('diseaseDetails');
        
        // Aquí cargarías los detalles desde el backend
        const diseases = @json($enfermedades);
        const disease = diseases.find(d => d.name === name);
        
        if (disease) {
            const colorClasses = {
                blue: 'bg-blue-100 text-blue-600',
                red: 'bg-red-100 text-red-600',
                green: 'bg-green-100 text-green-600',
                orange: 'bg-orange-100 text-orange-600',
                purple: 'bg-purple-100 text-purple-600',
                indigo: 'bg-indigo-100 text-indigo-600',
                teal: 'bg-teal-100 text-teal-600',
                gray: 'bg-gray-100 text-gray-600',
            };
            
            details.innerHTML = `
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 ${colorClasses[disease.color]} rounded-2xl flex items-center justify-center">
                        <i class="fas fa-activity text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">${disease.name}</h2>
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 mt-1">
                            ${disease.category}
                        </span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">📊 Estadísticas</p>
                        <p class="font-medium">${disease.patients.toLocaleString()} pacientes registrados</p>
                    </div>
                    
                    ${disease.description ? `
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">📝 Descripción</p>
                        <p class="text-gray-700">${disease.description}</p>
                    </div>
                    ` : ''}
                    
                    ${disease.symptoms ? `
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">⚠️ Síntomas Comunes</p>
                        <div class="flex flex-wrap gap-2">
                            ${disease.symptoms.split(',').map(symptom => 
                                `<span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">${symptom.trim()}</span>`
                            ).join('')}
                        </div>
                    </div>
                    ` : ''}
                    
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-blue-600 mb-2">💡 Recomendaciones</p>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <span>Control médico regular para monitorear la evolución</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <span>Adherencia al tratamiento prescrito</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <span>Estilo de vida saludable y seguimiento de síntomas</span>
                            </li>
                        </ul>
                    </div>
                </div>
            `;
        }
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function editDisease(id) {
        openDiseaseModal();
        document.getElementById('modalTitle').textContent = 'Editar Enfermedad';
        document.getElementById('formMethod').value = 'PUT';
        // Aquí cargarías los datos de la enfermedad para editar
    }

    // Cerrar modales al hacer clic fuera
    document.getElementById('diseaseModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeDiseaseModal();
    });
    document.getElementById('viewDiseaseModal')?.addEventListener('click', function(e) {
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