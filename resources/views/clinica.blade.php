<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Clínica Aguachica - Sistema Inteligente de Seguimiento de Pacientes Crónicos</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome (opcional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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
                <div class="flex items-center gap-2">
                    <div class="bg-blue-600 rounded-lg p-2">
                        <i class="fas fa-heart w-6 h-6 text-white text-xl"></i>
                    </div>
                    <span class="text-xl font-semibold text-gray-900">Clínica Aguachica</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#inicio" class="text-gray-700 hover:text-blue-600 transition-colors">Inicio</a>
                    <a href="#servicios" class="text-gray-700 hover:text-blue-600 transition-colors">Servicios</a>
                    <a href="#contacto" class="text-gray-700 hover:text-blue-600 transition-colors">Contacto</a>
                    <a href="{{ route('login') }}"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Iniciar Sesión
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
                    <a href="#nosotros" class="text-gray-700 hover:text-blue-600 transition-colors">Nosotros</a>
                    <a href="#servicios" class="text-gray-700 hover:text-blue-600 transition-colors">Servicios</a>
                    <a href="#contacto" class="text-gray-700 hover:text-blue-600 transition-colors">Contacto</a>
                    <a href="{{ route('login') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Iniciar Sesión
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
                        <i class="fas fa-bolt w-4 h-4"></i>
                        <span class="text-sm font-medium">Tecnología Médica Avanzada</span>
                    </div>

                    <h1
                        class="text-4xl sm:text-5xl lg:text-6xl text-gray-900 leading-tight animate-fade-in-up delay-100">
                        Transformando el seguimiento de pacientes crónicos con tecnología
                    </h1>

                    <p class="text-xl text-gray-600 animate-fade-in-up delay-200">
                        Sistema web diseñado para mejorar la atención médica, centralizar información clínica y
                        gestionar eficientemente pacientes crónicos.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 animate-fade-in-up delay-300">
                        <a href="{{ route('demo.solicitar') }}"
                            class="px-8 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 group">
                            Solicitar Demo
                            <i class="fas fa-chevron-right w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="{{ route('login') }}"
                            class="px-8 py-4 border-2 border-gray-300 text-gray-700 rounded-lg hover:border-blue-600 hover:text-blue-600 transition-colors text-center">
                            Ingresar al Sistema
                        </a>
                    </div>
                </div>

                <div class="relative animate-fade-in-up delay-200">
                    <div class="absolute -top-4 -left-4 w-24 h-24 bg-blue-200 rounded-full blur-3xl opacity-50"></div>
                    <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-green-200 rounded-full blur-3xl opacity-50">
                    </div>
                    <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwyfHxkb2N0b3IlMjB1c2luZyUyMGxhcHRvcCUyMG1lZGljYWwlMjB0ZWNobm9sb2d5JTIwaG9zcGl0YWx8ZW58MXx8fHwxNzc3NTIxODQwfDA&ixlib=rb-4.1.0&q=80&w=1080"
                        alt="Doctor usando laptop con dashboard médico digital"
                        class="relative rounded-2xl shadow-2xl w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="servicios" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl text-gray-900 mb-4 animate-fade-in-up">Características del Sistema</h2>
                <p class="text-xl text-gray-600 animate-fade-in-up delay-100">Tecnología médica de vanguardia para una
                    atención superior</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $features = [
                        ['icon' => 'fa-users', 'title' => 'Gestión de Pacientes', 'description' => 'Administre perfiles completos de pacientes con historial médico centralizado y accesible desde cualquier lugar.'],
                        ['icon' => 'fa-heartbeat', 'title' => 'Historial Clínico', 'description' => 'Registros médicos electrónicos completos con trazabilidad total de consultas, diagnósticos y tratamientos.'],
                        ['icon' => 'fa-bolt', 'title' => 'Alertas Inteligentes', 'description' => 'Notificaciones automáticas sobre citas, medicamentos y cambios críticos en el estado de salud del paciente.'],
                        ['icon' => 'fa-chart-line', 'title' => 'Dashboard en Tiempo Real', 'description' => 'Visualice métricas clave, tendencias y estadísticas de salud actualizadas instantáneamente.'],
                        ['icon' => 'fa-clock', 'title' => 'Gestión de Citas', 'description' => 'Programación y seguimiento de citas médicas con recordatorios automáticos para pacientes.'],
                        ['icon' => 'fa-shield-alt', 'title' => 'Seguridad de Datos', 'description' => 'Protección de nivel hospitalario con encriptación end-to-end y cumplimiento de normativas de salud.'],
                    ];
                @endphp

                @foreach($features as $index => $feature)
                    <div class="p-6 bg-white border border-gray-200 rounded-xl hover:shadow-lg hover:border-blue-300 transition-all group hover-scale"
                        style="animation: fadeInUp 0.6s ease-out {{ $index * 0.1 }}s forwards; opacity: 0;">
                        <div class="mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas {{ $feature['icon'] }} w-8 h-8 text-blue-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl text-gray-900 mb-3">{{ $feature['title'] }}</h3>
                        <p class="text-gray-600">{{ $feature['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-blue-600">
        <div class="max-w-7xl mx-auto">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @php
                    $stats = [
                        ['value' => '+1,000', 'label' => 'Pacientes Gestionados'],
                        ['value' => '95%', 'label' => 'Eficiencia Administrativa'],
                        ['value' => '24/7', 'label' => 'Acceso al Sistema'],
                        ['value' => '100%', 'label' => 'Disponibilidad'],
                    ];
                @endphp

                @foreach($stats as $index => $stat)
                    <div class="text-center animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s">
                        <div class="text-4xl sm:text-5xl text-white mb-2 font-bold">{{ $stat['value'] }}</div>
                        <div class="text-blue-100">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 to-blue-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl text-gray-900 mb-4 animate-fade-in-up">¿Cómo Funciona?</h2>
                <p class="text-xl text-gray-600 animate-fade-in-up delay-100">Proceso simple y eficiente en 4 pasos</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                @php
                    $steps = [
                        ['number' => '1', 'title' => 'Registrar Paciente', 'description' => 'Ingrese los datos del paciente y su historial médico en el sistema.'],
                        ['number' => '2', 'title' => 'Seguimiento Médico', 'description' => 'Monitoree consultas, tratamientos y evolución del paciente.'],
                        ['number' => '3', 'title' => 'Alertas y Recordatorios', 'description' => 'Reciba notificaciones automáticas sobre citas y medicamentos.'],
                        ['number' => '4', 'title' => 'Mejor Atención', 'description' => 'Tome decisiones informadas con datos centralizados y accesibles.'],
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

    <!-- Testimonials -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl text-gray-900 mb-4 animate-fade-in-up">Lo Que Dicen Nuestros
                    Profesionales</h2>
                <p class="text-xl text-gray-600 animate-fade-in-up delay-100">Testimonios de médicos y personal
                    administrativo</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @php
                    $testimonials = [
                        [
                            'quote' => 'Este sistema ha transformado completamente la forma en que gestionamos a nuestros pacientes crónicos. Tener toda la información centralizada nos permite brindar mejor atención.',
                            'author' => 'Dr. Carlos Martínez',
                            'role' => 'Médico Internista'
                        ],
                        [
                            'quote' => 'La eficiencia administrativa ha mejorado notablemente. Ahora podemos acceder al historial completo de cualquier paciente en segundos.',
                            'author' => 'Ana López',
                            'role' => 'Coordinadora Administrativa'
                        ],
                        [
                            'quote' => 'Las alertas automáticas de citas y medicamentos nos han ayudado a mejorar la adherencia al tratamiento. El dashboard en tiempo real es increíblemente útil.',
                            'author' => 'Dra. María Rodríguez',
                            'role' => 'Medicina Familiar'
                        ],
                    ];
                @endphp

                @foreach($testimonials as $index => $testimonial)
                    <div class="p-6 bg-white border border-gray-200 rounded-xl hover:shadow-lg transition-shadow animate-fade-in-up"
                        style="animation-delay: {{ $index * 0.1 }}s">
                        <div class="mb-4">
                            <i class="fas fa-check-circle w-8 h-8 text-green-500 text-2xl"></i>
                        </div>
                        <p class="text-gray-700 mb-6 italic">"{{ $testimonial['quote'] }}"</p>
                        <div>
                            <div class="text-gray-900 font-semibold">{{ $testimonial['author'] }}</div>
                            <div class="text-gray-500 text-sm">{{ $testimonial['role'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-600 to-blue-800">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl sm:text-4xl text-white mb-6 animate-fade-in-up">¿Listo para transformar su clínica?</h2>
            <p class="text-xl text-blue-100 mb-8 animate-fade-in-up delay-100">Únase a cientos de profesionales de la
                salud que ya confían en nuestro sistema</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up delay-200">
                <a href="{{ route('demo.solicitar') }}"
                    class="px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition-colors flex items-center justify-center gap-2 group">
                    Solicitar Demo Gratuita
                    <i class="fas fa-chevron-right w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="{{ route('contacto.ventas') }}"
                    class="px-8 py-4 border-2 border-white text-white rounded-lg hover:bg-white/10 transition-colors text-center">
                    Hablar con Ventas
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contacto" class="bg-gray-900 text-gray-300 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="bg-blue-600 rounded-lg p-2">
                            <i class="fas fa-heart w-6 h-6 text-white"></i>
                        </div>
                        <span class="text-xl font-semibold text-white">Clínica Aguachica</span>
                    </div>
                    <p class="text-gray-400">
                        Sistema Inteligente de Seguimiento de Pacientes Crónicos
                    </p>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4">Información</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition-colors">Sobre Nosotros</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Servicios</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Precios</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4">Soporte</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition-colors">Centro de Ayuda</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Documentación</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Términos de Uso</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Privacidad</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4">Contacto</h3>
                    <ul class="space-y-2">
                        <li><i class="fas fa-map-marker-alt mr-2"></i> Aguachica, Colombia</li>
                        <li><i class="fas fa-phone mr-2"></i> +57 300 123 4567</li>
                        <li><i class="fas fa-envelope mr-2"></i> info@clinicaaguachica.com</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-400">
                    © {{ date('Y') }} Clínica Médica Aguachica. Todos los derechos reservados.
                </p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition-colors"><i class="fab fa-facebook"></i> Facebook</a>
                    <a href="#" class="hover:text-white transition-colors"><i class="fab fa-twitter"></i> Twitter</a>
                    <a href="#" class="hover:text-white transition-colors"><i class="fab fa-linkedin"></i> LinkedIn</a>
                    <a href="#" class="hover:text-white transition-colors"><i class="fab fa-instagram"></i>
                        Instagram</a>
                </div>
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

</body>

</html>