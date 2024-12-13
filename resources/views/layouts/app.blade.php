<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">

    <!-- Fonts -->
    {{-- <link rel="dns-prefetch" href="//fonts.bunny.net"> --}}
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div id="app">
        @auth
            <!-- Navbar -->
            <nav class="bg-white shadow-sm">
                <div class="container mx-auto px-4">
                    <div class="flex items-center justify-between py-4">
                        <a class="flex items-center text-blue-500" href="{{ url('/home') }}">
                            <img src="{{ asset('logo.png') }}" alt="Logo" class="h-14 mr-0">
                        </a>
                        <button class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 md:hidden"
                            type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                            aria-controls="navbarSupportedContent" aria-expanded="false"
                            aria-label="{{ __('Toggle navigation') }}">
                            <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24">
                                <path d="M4 5h16M4 12h16m-7 7h7"></path>
                            </svg>
                        </button>
                        <div class="hidden md:flex items-center space-x-4" id="navbarSupportedContent">
                            <ul class="flex space-x-4">
                                <!-- Placeholder for left navbar items if needed -->
                            </ul>
                            <ul class="flex space-x-4">
                                @guest
                                    @if (Route::has('login'))
                                        <li>
                                            <a class="text-blue-500 hover:text-blue-700"
                                                href="{{ route('login') }}">{{ __('Login') }}</a>
                                        </li>
                                    @endif
                                    @if (Route::has('register'))
                                        <li>
                                            <a class="text-blue-500 hover:text-blue-700"
                                                href="{{ route('register') }}">{{ __('Register') }}</a>
                                        </li>
                                    @endif
                                @else
                                    <li class="relative">
                                        <button class="flex items-center text-blue-500 hover:text-blue-700 focus:outline-none"
                                            id="navbarDropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ Auth::user()->name }}
                                            <svg class="ml-2 h-4 w-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M12 16l-6-6h12z"></path>
                                            </svg>
                                        </button>
                                        <div class="hidden absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-md z-50"
                                            id="dropdown-menu">
                                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                href="{{ route('logout') }}"
                                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                                {{ __('Logout') }}
                                            </a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                class="hidden">
                                                @csrf
                                            </form>
                                        </div>
                                    </li>
                                @endguest
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="flex">
                <!-- Sidebar -->
                <aside id="sidebar" class="w-1/4 bg-white p-6 h-screen sticky top-0 left-0 transition-all duration-300">
                    <div class="sticky top-0 flex justify-between items-center">
                        <h5 class="text-lg font-bold mb-4">{{ __('Monitor de Incidentes') }}</h5>
                        <button id="toggleSidebar" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <nav class="flex flex-col space-y-2">
                        <a href="{{ url('/dashboard') }}"
                            class="text-blue-500 hover:bg-blue-100 px-4 py-2 rounded transition-colors duration-300">Dashboard</a>
                        <a href="{{ url('/map-dashboard') }}"
                            class="text-blue-500 hover:bg-blue-100 px-4 py-2 rounded transition-colors duration-300">Map
                            Dashboard</a>
                        <a href="{{ url('/dashboard') }}"
                            class="text-blue-500 hover:bg-blue-100 px-4 py-2 rounded transition-colors duration-300">Gráfico
                            de incidentes</a>
                    </nav>
                </aside>

                <!-- Main Content -->
                <main id="main-content" class="w-3/4 flex-grow p-6 transition-all duration-300">
                    @yield('content')
                </main>
            </div>
        @else
            <main class="w-full flex-grow p-0">
                @yield('content')
            </main>
        @endauth
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleSidebarButton = document.getElementById('toggleSidebar');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const dropdownButton = document.getElementById('navbarDropdown');
            const dropdownMenu = document.getElementById('dropdown-menu');

            toggleSidebarButton.addEventListener('click', function() {
                sidebar.classList.toggle('w-1/4');
                sidebar.classList.toggle('w-12');
                mainContent.classList.toggle('w-3/4');
                mainContent.classList.toggle('w-full');
            });

            dropdownButton.addEventListener('click', function() {
                dropdownMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', function(event) {
                if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>
