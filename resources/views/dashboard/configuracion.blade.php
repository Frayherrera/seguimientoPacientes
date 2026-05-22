@extends('layouts.dashboard')

@section('title', 'Configuración - Clínica Aguachica')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-semibold text-gray-900">Configuración</h1>
        <p class="text-gray-500 mt-1">Gestiona las preferencias del sistema</p>
    </div>

    <!-- Settings Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Perfil de Usuario -->
        <div onclick="openSettingsModal('profile')" 
             class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow cursor-pointer group">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                    <i class="fas fa-user text-blue-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Perfil de Usuario</h3>
                    <p class="text-sm text-gray-600">Actualiza tu información personal y contraseña</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600 transition-colors"></i>
            </div>
        </div>

        <!-- Notificaciones -->
        <div onclick="openSettingsModal('notifications')" 
             class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow cursor-pointer group">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-green-100 rounded-lg group-hover:bg-green-200 transition-colors">
                    <i class="fas fa-bell text-green-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Notificaciones</h3>
                    <p class="text-sm text-gray-600">Configura las alertas y notificaciones del sistema</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-600 transition-colors"></i>
            </div>
        </div>

        <!-- Seguridad -->
        <div onclick="openSettingsModal('security')" 
             class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow cursor-pointer group">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                    <i class="fas fa-shield-alt text-purple-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Seguridad</h3>
                    <p class="text-sm text-gray-600">Gestiona la seguridad y privacidad de tu cuenta</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-600 transition-colors"></i>
            </div>
        </div>

        <!-- Base de Datos -->
        <div onclick="openSettingsModal('database')" 
             class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow cursor-pointer group">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-orange-100 rounded-lg group-hover:bg-orange-200 transition-colors">
                    <i class="fas fa-database text-orange-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Base de Datos</h3>
                    <p class="text-sm text-gray-600">Configuración de respaldo y sincronización</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-orange-600 transition-colors"></i>
            </div>
        </div>

        <!-- Backup -->
        <div onclick="openSettingsModal('backup')" 
             class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow cursor-pointer group">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-teal-100 rounded-lg group-hover:bg-teal-200 transition-colors">
                    <i class="fas fa-cloud-upload-alt text-teal-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Respaldo</h3>
                    <p class="text-sm text-gray-600">Programa y gestiona respaldos automáticos</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-teal-600 transition-colors"></i>
            </div>
        </div>

        <!-- Integraciones -->
        <div onclick="openSettingsModal('integrations')" 
             class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow cursor-pointer group">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-indigo-100 rounded-lg group-hover:bg-indigo-200 transition-colors">
                    <i class="fas fa-plug text-indigo-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Integraciones</h3>
                    <p class="text-sm text-gray-600">Conecta con servicios externos</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-600 transition-colors"></i>
            </div>
        </div>
    </div>

    <!-- General Settings -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Configuración General</h3>
            <button onclick="saveGeneralSettings()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                <i class="fas fa-save mr-2"></i> Guardar Cambios
            </button>
        </div>
        <div class="space-y-4">
            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                <div>
                    <p class="text-sm font-medium text-gray-900">Modo Oscuro</p>
                    <p class="text-xs text-gray-500">Activa el tema oscuro en la interfaz</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="darkModeToggle" class="sr-only peer" onchange="toggleDarkMode()">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                <div>
                    <p class="text-sm font-medium text-gray-900">Notificaciones por Email</p>
                    <p class="text-xs text-gray-500">Recibe notificaciones importantes por correo</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="emailNotificationsToggle" class="sr-only peer" checked>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                <div>
                    <p class="text-sm font-medium text-gray-900">Alertas de Pacientes Críticos</p>
                    <p class="text-xs text-gray-500">Notificaciones inmediatas para casos críticos</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="criticalAlertsToggle" class="sr-only peer" checked>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                <div>
                    <p class="text-sm font-medium text-gray-900">Recordatorios de Citas</p>
                    <p class="text-xs text-gray-500">Alertas automáticas de citas programadas</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="appointmentRemindersToggle" class="sr-only peer" checked>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="flex items-center justify-between py-3">
                <div>
                    <p class="text-sm font-medium text-gray-900">Idioma</p>
                    <p class="text-xs text-gray-500">Selecciona el idioma de la interfaz</p>
                </div>
                <select id="languageSelect" class="px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="es">Español</option>
                    <option value="en">English</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Configuración -->
<div id="settingsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto transform transition-all">
        <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Configuración</h3>
            <button onclick="closeSettingsModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times w-6 h-6 text-xl"></i>
            </button>
        </div>
        <div class="p-6" id="modalContent">
            <!-- Contenido dinámico -->
        </div>
    </div>
</div>

<script>
    // Funciones para los modales
    function openSettingsModal(section) {
        const modal = document.getElementById('settingsModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalContent = document.getElementById('modalContent');
        
        const sections = {
            profile: {
                title: 'Perfil de Usuario',
                content: `
                    <form class="space-y-4" id="profileForm">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                            <input type="text" value="Dr. Carlos Martínez" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                            <input type="email" value="carlos@clinicaaguachica.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="tel" value="3001234567" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                            <input type="text" value="Medicina Interna" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="pt-4 border-t border-gray-200 mt-4">
                            <h4 class="font-medium text-gray-900 mb-3">Cambiar Contraseña</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual</label>
                                    <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                                    <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                                    <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button type="button" onclick="closeSettingsModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancelar</button>
                            <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Guardar Cambios</button>
                        </div>
                    </form>
                `
            },
            notifications: {
                title: 'Notificaciones',
                content: `
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-200">
                            <div>
                                <p class="font-medium text-gray-900">Notificaciones por Correo</p>
                                <p class="text-sm text-gray-500">Recibir alertas por email</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-gray-200">
                            <div>
                                <p class="font-medium text-gray-900">Notificaciones Push</p>
                                <p class="text-sm text-gray-500">Alertas en el navegador</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <p class="font-medium text-gray-900">Recordatorios SMS</p>
                                <p class="text-sm text-gray-500">Mensajes de texto para citas</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-6 mt-4">
                        <button onclick="closeSettingsModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancelar</button>
                        <button onclick="saveSettings()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Guardar</button>
                    </div>
                `
            },
            security: {
                title: 'Seguridad',
                content: `
                    <div class="space-y-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm text-blue-800 mb-2">✓ Verificación en dos pasos</p>
                            <p class="text-xs text-blue-600">Añade una capa extra de seguridad a tu cuenta</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sesiones Activas</label>
                            <div class="bg-gray-50 rounded-lg p-3 text-sm">
                                <p>Dispositivo actual - Chrome en Windows</p>
                                <p class="text-xs text-gray-500">Último acceso: Hoy a las 09:30 AM</p>
                            </div>
                        </div>
                        <div>
                            <button onclick="logoutAllDevices()" class="text-red-600 hover:text-red-700 text-sm font-medium">Cerrar sesión en todos los dispositivos</button>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-6">
                        <button onclick="closeSettingsModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cerrar</button>
                    </div>
                `
            },
            database: {
                title: 'Base de Datos',
                content: `
                    <div class="space-y-4">
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <p class="text-sm text-yellow-800 mb-1">⚠️ Respaldos Automáticos</p>
                            <p class="text-xs text-yellow-600">Último respaldo: ${new Date().toLocaleDateString()}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Frecuencia de Respaldo</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option>Diario</option>
                                <option selected>Semanal</option>
                                <option>Mensual</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación de Respaldo</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option>Local</option>
                                <option>Nube</option>
                                <option>Ambos</option>
                            </select>
                        </div>
                        <button onclick="manualBackup()" class="w-full py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-database mr-2"></i> Realizar Respaldo Ahora
                        </button>
                    </div>
                    <div class="flex gap-3 pt-6">
                        <button onclick="closeSettingsModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cerrar</button>
                    </div>
                `
            },
            backup: {
                title: 'Respaldo',
                content: `
                    <div class="space-y-4">
                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-sm text-green-800 mb-1">✓ Respaldo programado</p>
                            <p class="text-xs text-green-600">Próximo respaldo: ${new Date(new Date().setDate(new Date().getDate() + 7)).toLocaleDateString()}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora de Respaldo</label>
                            <input type="time" value="02:00" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Retención de Respaldos</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option>30 días</option>
                                <option selected>60 días</option>
                                <option>90 días</option>
                            </select>
                        </div>
                        <button class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-play mr-2"></i> Programar Respaldo
                        </button>
                    </div>
                    <div class="flex gap-3 pt-6">
                        <button onclick="closeSettingsModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cerrar</button>
                    </div>
                `
            },
            integrations: {
                title: 'Integraciones',
                content: `
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-200">
                            <div>
                                <p class="font-medium text-gray-900">API de Laboratorios</p>
                                <p class="text-sm text-gray-500">Conectar con laboratorios clínicos</p>
                            </div>
                            <button class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">Conectar</button>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-gray-200">
                            <div>
                                <p class="font-medium text-gray-900">Facturación Electrónica</p>
                                <p class="text-sm text-gray-500">Integración con DIAN</p>
                            </div>
                            <button class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">Configurar</button>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <p class="font-medium text-gray-900">Google Calendar</p>
                                <p class="text-sm text-gray-500">Sincronizar citas médicas</p>
                            </div>
                            <button class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">Conectar</button>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-6">
                        <button onclick="closeSettingsModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cerrar</button>
                    </div>
                `
            }
        };
        
        const sectionData = sections[section];
        if (sectionData) {
            modalTitle.textContent = sectionData.title;
            modalContent.innerHTML = sectionData.content;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
    
    function closeSettingsModal() {
        const modal = document.getElementById('settingsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    function saveSettings() {
        alert('Configuración guardada exitosamente');
        closeSettingsModal();
    }
    
    function saveGeneralSettings() {
        alert('Configuración general guardada');
    }
    
    function toggleDarkMode() {
        const isDark = document.getElementById('darkModeToggle').checked;
        if (isDark) {
            document.body.classList.add('dark');
        } else {
            document.body.classList.remove('dark');
        }
    }
    
    function logoutAllDevices() {
        if (confirm('¿Estás seguro de que deseas cerrar sesión en todos los dispositivos?')) {
            alert('Sesión cerrada en todos los dispositivos');
        }
    }
    
    function manualBackup() {
        alert('Iniciando respaldo manual...');
    }
    
    // Cerrar modal al hacer clic fuera
    document.getElementById('settingsModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeSettingsModal();
    });
    
    // Guardar perfil
    document.addEventListener('submit', function(e) {
        if (e.target.id === 'profileForm') {
            e.preventDefault();
            alert('Perfil actualizado exitosamente');
            closeSettingsModal();
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
    
    /* Dark mode styles (opcional) */
    .dark {
        background-color: #1a202c;
        color: #f7fafc;
    }
</style>
@endsection