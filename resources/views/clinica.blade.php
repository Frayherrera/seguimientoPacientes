<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Clínica Aguachica - Sistema de Gestión y Seguimiento de Pacientes Crónicos</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome (opcional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="manifest" href="/manifest.json">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <link rel="apple-touch-icon" sizes="16x16" href="/pwa/icons/ios/16.png">
    <link rel="apple-touch-icon" sizes="20x20" href="/pwa/icons/ios/20.png">
    <link rel="apple-touch-icon" sizes="29x29" href="/pwa/icons/ios/29.png">
    <link rel="apple-touch-icon" sizes="32x32" href="/pwa/icons/ios/32.png">
    <link rel="apple-touch-icon" sizes="40x40" href="/pwa/icons/ios/40.png">
    <link rel="apple-touch-icon" sizes="50x50" href="/pwa/icons/ios/50.png">
    <link rel="apple-touch-icon" sizes="57x57" href="/pwa/icons/ios/57.png">
    <link rel="apple-touch-icon" sizes="58x58" href="/pwa/icons/ios/58.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/pwa/icons/ios/60.png">
    <link rel="apple-touch-icon" sizes="64x64" href="/pwa/icons/ios/64.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/pwa/icons/ios/72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/pwa/icons/ios/76.png">
    <link rel="apple-touch-icon" sizes="80x80" href="/pwa/icons/ios/80.png">
    <link rel="apple-touch-icon" sizes="87x87" href="/pwa/icons/ios/87.png">
    <link rel="apple-touch-icon" sizes="100x100" href="/pwa/icons/ios/100.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/pwa/icons/ios/114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/pwa/icons/ios/120.png">
    <link rel="apple-touch-icon" sizes="128x128" href="/pwa/icons/ios/128.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/pwa/icons/ios/144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/pwa/icons/ios/152.png">
    <link rel="apple-touch-icon" sizes="167x167" href="/pwa/icons/ios/167.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/pwa/icons/ios/180.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/pwa/icons/ios/192.png">
    <link rel="apple-touch-icon" sizes="256x256" href="/pwa/icons/ios/256.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/pwa/icons/ios/512.png">
    <link rel="apple-touch-icon" sizes="1024x1024" href="/pwa/icons/ios/1024.png">

    <link href="/pwa/icons/ios/1024.png" sizes="1024x1024" rel="apple-touch-startup-image">
    <link href="/pwa/icons/ios/512.png" sizes="512x512" rel="apple-touch-startup-image">
    <link href="/pwa/icons/ios/256.png" sizes="256x256" rel="apple-touch-startup-image">
    <link href="/pwa/icons/ios/192.png" sizes="192x192" rel="apple-touch-startup-image">


    <style>
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Animaciones personalizadas */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        .delay-400 {
            animation-delay: 0.4s;
        }

        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }

        .hover-scale:hover {
            transform: translateY(-5px);
        }

        /* Backdrop blur para navbar */
        .bg-white\/95 {
            background-color: rgba(255, 255, 255, 0.95);
        }
    </style>
</head>

<body class="min-h-screen bg-white">

    <!-- Navbar -->
    <nav class="fixed top-0 w-full bg-white/95 backdrop-blur-sm border-b border-gray-200 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('pwa/icons/android/launchericon-512x512.png') }}"
                        alt="Clínica Médica Aguachica" class="h-12 w-12 rounded-lg object-contain shadow-sm">
                    <span class="text-xl font-semibold text-gray-900">Clínica Aguachica</span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#inicio" class="text-gray-700 hover:text-blue-600 transition-colors">Inicio</a>
                    <a href="#servicios" class="text-gray-700 hover:text-blue-600 transition-colors">Módulos</a>
                    <a href="#contacto" class="text-gray-700 hover:text-blue-600 transition-colors">Contacto</a>
                    <a href="{{ auth()->check() ? route('dashboard.index') : route('login') }}"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        {{ auth()->check() ? 'Entrar al Sistema' : 'Iniciar Sesión' }}
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobileMenuButton" class="md:hidden p-2" aria-label="Menú">
                    <i id="menuIconOpen" class="fas fa-bars w-6 h-6 text-2xl"></i>
                    <i id="menuIconClose" class="fas fa-times w-6 h-6 text-2xl hidden"></i>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden py-4 border-t border-gray-200 animate-fade-in">
                <div class="flex flex-col gap-4">
                    <a href="#inicio" class="text-gray-700 hover:text-blue-600 transition-colors">Inicio</a>
                    <a href="#servicios" class="text-gray-700 hover:text-blue-600 transition-colors">Módulos</a>
                    <a href="#contacto" class="text-gray-700 hover:text-blue-600 transition-colors">Contacto</a>
                    <a href="{{ auth()->check() ? route('dashboard.index') : route('login') }}"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        {{ auth()->check() ? 'Entrar al Sistema' : 'Iniciar Sesión' }}
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="inicio" class="pt-24 pb-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-50 via-white to-green-50">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-700 rounded-full animate-fade-in-up">
                        <i class="fas fa-stethoscope w-4 h-4"></i>
                        <span class="text-sm font-medium">Sistema clínico de uso interno</span>
                    </div>

                    <h1
                        class="text-4xl sm:text-5xl lg:text-6xl text-gray-900 leading-tight animate-fade-in-up delay-100">
                        Seguimiento de pacientes crónicos en un solo lugar
                    </h1>

                    <p class="text-xl text-gray-600 animate-fade-in-up delay-200">
                        Plataforma web para registrar pacientes y enfermedades, monitorear controles con signos
                        vitales, generar alertas por umbrales, gestionar citas y consultar reportes clínicos.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 animate-fade-in-up delay-300">
                        <a href="{{ auth()->check() ? route('dashboard.index') : route('login') }}"
                            class="px-8 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 group">
                            {{ auth()->check() ? 'Entrar al Sistema' : 'Ingresar al Sistema' }}
                            <i class="fas fa-chevron-right w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="#servicios"
                            class="px-8 py-4 border-2 border-gray-300 text-gray-700 rounded-lg hover:border-blue-600 hover:text-blue-600 transition-colors text-center">
                            Conocer los módulos
                        </a>
                    </div>
                </div>

                <div class="relative animate-fade-in-up delay-200">
                    <div class="absolute -top-4 -left-4 w-24 h-24 bg-blue-200 rounded-full blur-3xl opacity-50"></div>
                    <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-green-200 rounded-full blur-3xl opacity-50">
                    </div>
                    <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwyfHxkb2N0b3IlMjB1c2luZyUyMGxhcHRvcCUyMG1lZGljYWwlMjB0ZWNobm9sb2d5JTIwaG9zcGl0YWx8ZW58MXx8fHwxNzc3NTIxODQwfDA&ixlib=rb-4.1.0&q=80&w=1080"
                        alt="Personal médico consultando un sistema de historias clínicas"
                        class="relative rounded-2xl shadow-2xl w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="servicios" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl text-gray-900 mb-4 animate-fade-in-up">Módulos del Sistema</h2>
                <p class="text-xl text-gray-600 animate-fade-in-up delay-100">Las funciones disponibles hoy en la
                    plataforma</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $features = [
                        ['icon' => 'fa-users', 'title' => 'Gestión de Pacientes', 'description' => 'Registre y actualice los datos de cada paciente, las enfermedades que padece y su información de contacto.'],
                        ['icon' => 'fa-heartbeat', 'title' => 'Seguimiento Médico', 'description' => 'Registre controles con signos vitales. El IMC se calcula automáticamente a partir del peso y la talla.'],
                        ['icon' => 'fa-bell', 'title' => 'Alertas por Umbrales', 'description' => 'Cada control se evalúa contra los umbrales configurados por enfermedad y genera alertas según su severidad.'],
                        ['icon' => 'fa-file-medical-alt', 'title' => 'Historial Clínico', 'description' => 'Consulte el historial en modo solo lectura con filtros por paciente, diagnóstico, médico y período. Exporte a CSV.'],
                        ['icon' => 'fa-calendar-check', 'title' => 'Gestión de Citas', 'description' => 'Agende citas y controle su flujo: Programada, Confirmada, Atendida, Cancelada o No Presentado, sin cruces de horario.'],
                        ['icon' => 'fa-chart-line', 'title' => 'Reportes', 'description' => 'Analice pacientes, citas, prevalencia de enfermedades y alertas por severidad en distintos períodos. Exporte a CSV.'],
                        ['icon' => 'fa-user-shield', 'title' => 'Usuarios y Acceso', 'description' => 'Administre usuarios con rol y estado activo o inactivo. El personal accede con sus propias credenciales.'],
                        ['icon' => 'fa-sliders-h', 'title' => 'Configuración', 'description' => 'Ajuste los umbrales de alerta por enfermedad y actualice su perfil, contraseña y sesiones activas.'],
                    ];
                @endphp

                @foreach($features as $index => $feature)
                    <div class="p-6 bg-white border border-gray-200 rounded-xl hover:shadow-lg hover:border-blue-300 transition-all group hover-scale"
                        style="animation: fadeInUp 0.6s ease-out {{ $index * 0.1 }}s forwards; opacity: 0;">
                        <div class="mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas {{ $feature['icon'] }} w-8 h-8 text-blue-600 text-3xl"></i>
                        </div>
                        <h3 class="text-lg text-gray-900 mb-3">{{ $feature['title'] }}</h3>
                        <p class="text-gray-600 text-sm">{{ $feature['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Modules Summary -->
    <section class="py-12 px-4 sm:px-6 lg:px-8 bg-blue-600">
        <div class="max-w-7xl mx-auto">
            <p class="text-center text-blue-100 mb-6 text-sm font-semibold uppercase tracking-wide">Módulos integrados
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                @foreach(['Pacientes', 'Enfermedades', 'Seguimiento médico', 'Historial clínico', 'Citas', 'Reportes', 'Usuarios', 'Configuración'] as $modulo)
                    <span
                        class="px-4 py-2 rounded-full bg-blue-500/60 text-white text-sm font-medium">{{ $modulo }}</span>
                @endforeach
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 to-blue-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl text-gray-900 mb-4 animate-fade-in-up">¿Cómo Funciona?</h2>
                <p class="text-xl text-gray-600 animate-fade-in-up delay-100">El flujo de trabajo dentro del sistema</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                @php
                    $steps = [
                        ['number' => '1', 'title' => 'Registrar Paciente', 'description' => 'Cree el expediente del paciente y asigne las enfermedades que serán monitoreadas.'],
                        ['number' => '2', 'title' => 'Registrar Control', 'description' => 'Ingrese los signos vitales y las notas de la atención médica en Seguimiento.'],
                        ['number' => '3', 'title' => 'Recibir Alertas', 'description' => 'El sistema compara los valores con los umbrales y genera una alerta por parámetro fuera de rango.'],
                        ['number' => '4', 'title' => 'Consultar y Exportar', 'description' => 'Revise el historial clínico, gestione las citas y descargue reportes en CSV.'],
                    ];
                @endphp

                @foreach($steps as $index => $step)
                    <div class="relative text-center animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 text-white rounded-full text-2xl font-bold mb-4">
                            {{ $step['number'] }}
                        </div>
                        <h3 class="text-xl text-gray-900 mb-3">{{ $step['title'] }}</h3>
                        <p class="text-gray-600">{{ $step['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Access and traceability -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl text-gray-900 mb-4 animate-fade-in-up">Control de Acceso y Registro Clínico</h2>
                <p class="text-xl text-gray-600 animate-fade-in-up delay-100">Información protegida y trazable para el
                    personal autorizado</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @php
                    $guarantees = [
                        [
                            'icon' => 'fa-lock',
                            'title' => 'Acceso autenticado',
                            'description' => 'El sistema requiere inicio de sesión con correo y contraseña. Cada usuario puede cerrar sesión en sus otros dispositivos.',
                        ],
                        [
                            'icon' => 'fa-user-check',
                            'title' => 'Roles y estado del usuario',
                            'description' => 'Cada usuario tiene un rol asignado y puede estar activo o inactivo. Nadie puede eliminar ni desactivar su propia cuenta.',
                        ],
                        [
                            'icon' => 'fa-notes-medical',
                            'title' => 'Trazabilidad clínica',
                            'description' => 'Cada control genera una historia clínica con sus signos vitales y alertas. Los registros clínicos no se eliminan.',
                        ],
                    ];
                @endphp

                @foreach($guarantees as $index => $item)
                    <div class="p-6 bg-white border border-gray-200 rounded-xl hover:shadow-lg transition-shadow animate-fade-in-up"
                        style="animation-delay: {{ $index * 0.1 }}s">
                        <div class="mb-4">
                            <i class="fas {{ $item['icon'] }} w-8 h-8 text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg text-gray-900 font-semibold mb-3">{{ $item['title'] }}</h3>
                        <p class="text-gray-600 text-sm">{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-600 to-blue-800">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl sm:text-4xl text-white mb-6 animate-fade-in-up">Centralice el seguimiento de sus
                pacientes crónicos</h2>
            <p class="text-xl text-blue-100 mb-8 animate-fade-in-up delay-100">Acceda con sus credenciales para
                gestionar pacientes, controles, citas y reportes desde un solo lugar.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up delay-200">
                <a href="{{ auth()->check() ? route('dashboard.index') : route('login') }}"
                    class="px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition-colors flex items-center justify-center gap-2 group">
                    {{ auth()->check() ? 'Ir al Panel' : 'Iniciar Sesión' }}
                    <i class="fas fa-chevron-right w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="#servicios"
                    class="px-8 py-4 border-2 border-white text-white rounded-lg hover:bg-white/10 transition-colors text-center">
                    Ver Módulos
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contacto" class="bg-gray-900 text-gray-300 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="rounded-lg bg-white p-1">
                            <img src="{{ asset('pwa/icons/android/launchericon-512x512.png') }}"
                                alt="Clínica Médica Aguachica" class="h-12 w-12 rounded object-contain">
                        </div>
                        <span class="text-xl font-semibold text-white">Clínica Aguachica</span>
                    </div>
                    <p class="text-gray-400 max-w-md">
                        Sistema de Gestión y Seguimiento de Pacientes Crónicos: pacientes, enfermedades, controles,
                        historial clínico, citas y reportes.
                    </p>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4">Navegación</h3>
                    <ul class="space-y-2">
                        <li><a href="#inicio" class="hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="#servicios" class="hover:text-white transition-colors">Módulos</a></li>
                        <li><a href="#contacto" class="hover:text-white transition-colors">Contacto</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4">Acceso</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ auth()->check() ? route('dashboard.index') : route('login') }}"
                                class="hover:text-white transition-colors">
                                {{ auth()->check() ? 'Panel del sistema' : 'Iniciar sesión' }}
                            </a>
                        </li>
                        <li class="text-gray-400"><i class="fas fa-map-marker-alt mr-2"></i>Aguachica, Colombia</li>
                        <li class="text-gray-400 text-sm">Sistema de uso interno para personal autorizado.</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8">
                <p class="text-gray-400 text-center">
                    © {{ date('Y') }} Clínica Médica Aguachica. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu JavaScript -->
    <script>
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        const menuIconOpen = document.getElementById('menuIconOpen');
        const menuIconClose = document.getElementById('menuIconClose');

        mobileMenuButton.addEventListener('click', () => {
            const isHidden = mobileMenu.classList.contains('hidden');
            if (isHidden) {
                mobileMenu.classList.remove('hidden');
                menuIconOpen.classList.add('hidden');
                menuIconClose.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
                menuIconOpen.classList.remove('hidden');
                menuIconClose.classList.add('hidden');
            }
        });

        // Cerrar mobile menu al hacer click en un enlace
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                menuIconOpen.classList.remove('hidden');
                menuIconClose.classList.add('hidden');
            });
        });
    </script>
    @vite(['resources/js/app.js'])
</body>

</html>
