<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('template/img/svg/logo.svg') }}" type="image/x-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Main Styles and Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Sidebar & Navbar Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div id="app">
        @auth
            <a class="skip-link sr-only" href="#main-content">Skip to content</a>
            <div class="page-flex">
                <!-- Sidebar -->
                <aside class="sidebar">
                    <div class="sidebar-start">
                        <div class="sidebar-head">
                            <a href="/" class="logo-wrapper" aria-label="Home">
                                <span class="icon logo" aria-hidden="true"></span>
                            </a>
                            <button class="sidebar-toggle transparent-btn" aria-label="Toggle menu" type="button">
                                <span class="icon menu-toggle" aria-hidden="true"></span>
                            </button>
                        </div>

                        <div class="sidebar-body">
                            <ul class="sidebar-body-menu">
                                <li>
                                    <span class="system-menu__title">Monitoramento</span>
                                    <a class="show-cat-btn" href="#" onclick="toggleMenu('monitor')"
                                        aria-label="Monitor options">
                                        <span class="icon document" id="icon-monitor" aria-hidden="true"></span>
                                        <span id="text-monitor">Monitor</span>
                                        <span class="category__btn transparent-btn" aria-label="Open list">
                                            <span class="icon arrow-down" aria-hidden="true"></span>
                                        </span>
                                    </a>
                                    <ul class="cat-sub-menu" id="monitor">
                                        <li><a href="map">Mapa</a></li>
                                        <li><a href="ticket">Tickets</a></li>
                                        <li><a href="graphic">Gráfico</a></li>
                                    </ul>
                                </li>

                                <li>
                                    <span class="system-menu__title">Backup</span>
                                    <a class="show-cat-btn" href="#" onclick="toggleMenu('backup')"
                                        aria-label="Backup options">
                                        <span class="icon dropbox" id="icon-backup" aria-hidden="true"></span>
                                        <span id="text-backup">BK Server</span>
                                        <span class="category__btn transparent-btn" aria-label="Open list">
                                            <span class="icon arrow-down" aria-hidden="true"></span>
                                        </span>
                                    </a>
                                    <ul class="cat-sub-menu" id="backup">
                                        <li><a href="bkserver/hosts">Hosts</a></li>
                                    </ul>

                                </li>
                                <li>
                                    <span class="system-menu__title">Executar Comandos</span>

                                    <ul class="cat-sub-menu" id="runbackup">
                                        <li><a href="bkserver/hosts">Backup</a></li>
                                    </ul>
                                    {{--                                     <ul class="cat-sub-menu" id="runbackup">
                                        <li>
                                            @isset($idCompany)
                                                <form method="POST"
                                                    action="{{ route('sync.devices', ['id_company' => $idCompany]) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary">Sincronizar</button>
                                                </form>
                                            @else
                                                <p>ID da empresa não definido</p>
                                            @endisset
                                        </li>
                                    </ul> --}}

                                <li>
                                    <a href="{{ route('run-syncdevices', ['id_company' => $idCompany]) }}"
                                        class="btn btn-primary">Run Syncdevices</a>
                                </li>


                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif


                                </li>
                            </ul>
                        </div>

                    </div>
                    <!-- Logout Button -->
                    <div class="sidebar-end">
                        <form method="POST" action="{{ route('logout') }}" class="flex items-center justify-center">
                            @csrf
                            <button type="submit"
                                class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </aside>

                <!-- Main Wrapper -->
                <div class="main-wrapper">
                    <!-- Main Navigation -->
                    <nav class="main-nav--bg">
                        <div class="container main-nav">
                            {{-- <div class="main-nav-center">
                                <img src="{{ $logoBase64 }}" alt="Logo" class="navbar-logo" width="150">
                            </div> --}}
                            <div class="main-nav-end flex items-center space-x-4">
                                <button class="sidebar-toggle transparent-btn" aria-label="Toggle menu" type="button">
                                    <span class="icon menu-toggle--gray" aria-hidden="true"></span>
                                </button>

                                <button class="theme-switcher gray-circle-btn" type="button" aria-label="Switch theme">
                                    <i class="sun-icon" data-feather="sun" aria-hidden="true"></i>
                                    <i class="moon-icon" data-feather="moon" aria-hidden="true"></i>
                                </button>

                                <div class="notification-wrapper">
                                    <ul class="users-item-dropdown notification-dropdown dropdown">
                                        <li>
                                            <a href="##">
                                                <div class="notification-dropdown-icon info">
                                                    <i data-feather="check"></i>
                                                </div>
                                                <div class="notification-dropdown-text">
                                                    <span class="notification-dropdown__title">System just updated</span>
                                                    <span class="notification-dropdown__subtitle">The system has been
                                                        successfully upgraded. Read more here.</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="##">
                                                <div class="notification-dropdown-icon danger">
                                                    <i data-feather="info" aria-hidden="true"></i>
                                                </div>
                                                <div class="notification-dropdown-text">
                                                    <span class="notification-dropdown__title">The cache is full!</span>
                                                    <span class="notification-dropdown__subtitle">Unnecessary caches take
                                                        up a lot of memory space and interfere...</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="##">
                                                <div class="notification-dropdown-icon info">
                                                    <i data-feather="check" aria-hidden="true"></i>
                                                </div>
                                                <div class="notification-dropdown-text">
                                                    <span class="notification-dropdown__title">New Subscriber here!</span>
                                                    <span class="notification-dropdown__subtitle">A new subscriber has
                                                        subscribed.</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="link-to-page" href="##">Go to Notifications page</a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="nav-user-wrapper">
                                    <div class="relative inline-block text-left">
                                        <button onclick="toggleDropdown()" class="nav-user-btn dropdown-btn"
                                            aria-label="My profile">
                                            <span class="nav-user-img">
                                                <picture>
                                                    <source srcset="{{ asset('img/avatar/avatar-illustrated-02.webp') }}"
                                                        type="image/webp">
                                                    <img src="{{ asset('img/avatar/avatar-illustrated-02.png') }}">
                                                </picture>
                                            </span>
                                        </button>
                                        <!-- Dropdown -->
                                        <div id="dropdownMenu"
                                            class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded-md shadow-lg">
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <a href="#"
                                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Logout
                                                </a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- Main Content -->
                    <main id="main-content">
                        @yield('content')
                    </main>

                    <!-- Footer -->
                    <footer class="footer">
                        <div class="container footer--flex">
                            <div class="footer-start">
                                <p><span id="current-year"></span> © K3G Solutions - <a href="https://k3gsolutions.com.br"
                                        target="_blank" rel="noopener noreferrer">k3gsolutions.com.br</a></p>
                            </div>
                            {{--                             <ul class="footer-end">
                                <li><a href="##">About</a></li>
                                <li><a href="##">Support</a></li>
                                <li><a href="##">Purchase</a></li>
                            </ul> --}}
                        </div>
                    </footer>
                </div>
            </div>
        @else
            <main class="w-full flex-grow p-0">
                @yield('content')
            </main>
        @endauth
    </div>

    <!-- Sidebar & Navbar Specific Scripts -->
    <script src="{{ asset('plugins/chart.min.js') }}" defer></script>
    <script src="{{ asset('plugins/feather.min.js') }}" defer></script>
    <script src="{{ asset('js/script.js') }}" defer></script>

    <script>
        function toggleDropdown() {
            const dropdownMenu = document.getElementById('dropdownMenu');
            dropdownMenu.classList.toggle('hidden');
        }

        function deleteAllCookies() {
            var cookies = document.cookie.split("; ");
            for (var i = 0; i < cookies.length; i++) {
                var cookie = cookies[i];
                var eqPos = cookie.indexOf("=");
                var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
            }
        }

        function logoutAndRedirect() {
            // Excluir todos os cookies
            deleteAllCookies();
            // Redirecionar para a página de login
            window.location.href = '/login';
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const yearSpan = document.getElementById('current-year');
            const currentYear = new Date().getFullYear();
            yearSpan.textContent = currentYear;
        });
    </script>

    <script>
        // Função para alterar o estilo com base nas permissões do usuário
        function setUserPermissions(user) {
            if (!user.hasMonitorAccess) {
                document.getElementById('monitor').style.display = 'none';
                document.getElementById('icon-monitor').className = 'icon lock';
                document.getElementById('text-monitor').style.color = 'red';
            }

            if (!user.hasBackupAccess) {
                document.getElementById('backup').style.display = 'none';
                document.getElementById('icon-backup').className = 'icon lock';
                document.getElementById('text-backup').style.color = 'red';
            }
        }

        // Simulação de requisição AJAX para obter permissões do usuário
        function fetchUserPermissions(userId) {
            fetch(`/api/user-permissions?userId=${userId}`)
                .then(response => response.json())
                .then(data => setUserPermissions(data))
                .catch(error => console.error('Error fetching permissions:', error));
        }

        // Id do usuário atual (no seu sistema isso pode vir da sessão ou de como a autenticação estiver configurada)
        const currentUserId = 'user123';

        // Obtenha as permissões e aplique-as na interface
        fetchUserPermissions(currentUserId);
    </script>

</body>

</html>
