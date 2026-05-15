<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - Clínica Aguachica')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Sidebar transition */
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }

        /* Custom scrollbar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: #1f2937;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 3px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        /* Animations */
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

    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 z-30 bg-black/50 lg:hidden hidden animate-fade-in"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 z-40 h-screen w-64 bg-gray-900 sidebar-transition -translate-x-full lg:translate-x-0">
        <div class="flex h-full flex-col">
            <!-- Logo -->
            <div class="flex items-center gap-3 border-b border-gray-800 p-6">
                <div class="bg-blue-600 rounded-lg p-2">
                    <i class="fas fa-heart w-6 h-6 text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-white font-semibold">Clínica Aguachica</h1>
                    <p class="text-xs text-gray-400">Sistema de Gestión</p>
                </div>
            </div>

            <!-- Menu Items -->
            <nav class="flex-1 overflow-y-auto p-4 sidebar-scroll">
                <ul class="space-y-1">
                    @php
                        $menuItems = [
                            ['icon' => 'fa-tachometer-alt', 'label' => 'Dashboard', 'path' => route('dashboard.index')],
                            ['icon' => 'fa-users', 'label' => 'Usuarios', 'path' => route('dashboard.usuarios')],
                            ['icon' => 'fa-user-circle', 'label' => 'Pacientes', 'path' => route('dashboard.pacientes')],
                            ['icon' => 'fa-heartbeat', 'label' => 'Enfermedades', 'path' => route('dashboard.enfermedades')],
                            ['icon' => 'fa-stethoscope', 'label' => 'Seguimiento Médico', 'path' => route('dashboard.seguimiento')],
                            ['icon' => 'fa-file-alt', 'label' => 'Historial Clínico', 'path' => route('dashboard.historial')],
                            ['icon' => 'fa-calendar-alt', 'label' => 'Citas', 'path' => route('dashboard.citas')],
                            ['icon' => 'fa-chart-line', 'label' => 'Reportes', 'path' => route('dashboard.reportes')],
                            ['icon' => 'fa-cog', 'label' => 'Configuración', 'path' => route('dashboard.configuracion')],
                        ];
                    @endphp

                    @foreach($menuItems as $item)
                        @php
                            $isActive = request()->routeIs($item['route'] ?? '') || request()->path() === $item['path'];
                        @endphp
                        <li>
                            <a href="{{ $item['path'] }}"
                               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm transition-colors {{ $isActive ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                                <i class="fas {{ $item['icon'] }} w-5 h-5"></i>
                                <span>{{ $item['label'] }}</span>
                                @if($isActive)
                                    <i class="fas fa-chevron-right ml-auto w-4 h-4"></i>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <!-- Logout -->
            <div class="border-t border-gray-800 p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                        <i class="fas fa-sign-out-alt w-5 h-5"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:pl-64">
        <!-- Top Header -->
        <header class="sticky top-0 z-30 bg-white border-b border-gray-200">
            <div class="flex items-center justify-between px-4 py-4">
                <!-- Mobile menu button -->
                <button id="mobileMenuButton" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                    <i id="menuIconOpen" class="fas fa-bars w-6 h-6 text-2xl"></i>
                    <i id="menuIconClose" class="fas fa-times w-6 h-6 text-2xl hidden"></i>
                </button>

                <!-- Search Bar -->
                <div class="flex-1 max-w-2xl mx-4">
                    <form action="{{ route('buscar') }}" method="GET" class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text"
                               name="q"
                               placeholder="Buscar pacientes, citas, historiales..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </form>
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-4">
                    <!-- Notifications -->
                    <button id="notificationsButton" class="relative p-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-bell w-6 h-6 text-gray-600 text-xl"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- User Profile -->
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                        <div class="hidden sm:block text-right">
                            <p class="text-sm font-medium text-gray-900">
                                {{ Auth::user()->name ?? 'Dr. Admin' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ Auth::user()->role ?? 'Administrador' }}
                            </p>
                        </div>
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold">
                                {{ strtoupper(substr(Auth::user()->name ?? 'DA', 0, 2)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6">
            @yield('content')
        </main>
    </div>

    <!-- JavaScript para funcionalidades interactivas -->
    <script>
        // Mobile Sidebar Toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const menuIconOpen = document.getElementById('menuIconOpen');
        const menuIconClose = document.getElementById('menuIconClose');

        function toggleSidebar() {
            const isOpen = !sidebar.classList.contains('-translate-x-full');
            
            if (isOpen) {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
                if (menuIconOpen && menuIconClose) {
                    menuIconOpen.classList.remove('hidden');
                    menuIconClose.classList.add('hidden');
                }
            } else {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
                if (menuIconOpen && menuIconClose) {
                    menuIconOpen.classList.add('hidden');
                    menuIconClose.classList.remove('hidden');
                }
            }
        }

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', toggleSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', toggleSidebar);
        }

        // Notifications dropdown (opcional)
        const notificationsButton = document.getElementById('notificationsButton');
        if (notificationsButton) {
            notificationsButton.addEventListener('click', () => {
                // Aquí puedes implementar un dropdown de notificaciones
                console.log('Notificaciones clickeado');
            });
        }

        // Cerrar sidebar al hacer click en un enlace (mobile)
        const sidebarLinks = document.querySelectorAll('#sidebar a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>