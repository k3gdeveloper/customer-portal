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
    {{--     <link rel="stylesheet" href="{{ asset('template/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material-dashboard.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div id="app">
        @auth
            <a class="skip-link sr-only" href="#skip-target">Skip to content</a>
            <div class="page-flex">
                <!-- Sidebar -->
                <aside class="sidebar">
                    <div class="sidebar-start">
                        <div class="sidebar-head">
                            <a href="/" class="logo-wrapper" title="Home">
                                <span class="sr-only">Home</span>
                                <span class="icon logo" aria-hidden="true"></span>
                                {{--                                 <div class="logo-text">
                                    <span class="logo-title">Elegant</span>
                                    <span class="logo-subtitle">Dashboard</span>
                                </div> --}}
                            </a>
                            <button class="sidebar-toggle transparent-btn" title="Menu" type="button">
                                <span class="sr-only">Toggle menu</span>
                                <span class="icon menu-toggle" aria-hidden="true"></span>
                            </button>
                        </div>
                        <div class="sidebar-body">

                            <ul class="sidebar-body-menu">
                                <li>
                                    <a class="active" href="/"><span class="icon home"
                                            aria-hidden="true"></span>Dashboard</a>
                                </li>
                            </ul>
                            <ul class="sidebar-body-menu">

                                <span class="system-menu__title">Monitoramento</span>
                                <li>
                                    <a class="show-cat-btn" href="#">
                                        <span class="icon document" aria-hidden="true"></span>Monitor
                                        <span class="category__btn transparent-btn" title="Open list">
                                            <span class="sr-only">Open list</span>
                                            <span class="icon arrow-down" aria-hidden="true"></span>
                                        </span>
                                    </a>
                                    <ul class="cat-sub-menu">
                                        <li><a href="map-dashboard">Mapa</a></li>
                                        <li><a href="dashboard">Tickets</a></li>
                                        <li><a href="new-post.html">Gráfico</a></li>
                                    </ul>
                                </li>

                                {{--                                 <li>
                                    <a class="show-cat-btn" href="#">
                                        <span class="icon folder" aria-hidden="true"></span>Categories
                                        <span class="category__btn transparent-btn" title="Open list">
                                            <span class="sr-only">Open list</span>
                                            <span class="icon arrow-down" aria-hidden="true"></span>
                                        </span>
                                    </a>
                                    <ul class="cat-sub-menu">
                                        <li><a href="categories.html">All categories</a></li>
                                    </ul>
                                </li> --}}
                                {{--                                 <li>
                                    <a class="show-cat-btn" href="#">
                                        <span class="icon image" aria-hidden="true"></span>Media
                                        <span class="category__btn transparent-btn" title="Open list">
                                            <span class="sr-only">Open list</span>
                                            <span class="icon arrow-down" aria-hidden="true"></span>
                                        </span>
                                    </a>
                                    <ul class="cat-sub-menu">
                                        <li><a href="media-01.html">Media-01</a></li>
                                        <li><a href="media-02.html">Media-02</a></li>
                                    </ul>
                                </li> --}}
                                {{--                                 <li>
                                    <a class="show-cat-btn" href="#">
                                        <span class="icon paper" aria-hidden="true"></span>Pages
                                        <span class="category__btn transparent-btn" title="Open list">
                                            <span class="sr-only">Open list</span>
                                            <span class="icon arrow-down" aria-hidden="true"></span>
                                        </span>
                                    </a>
                                    <ul class="cat-sub-menu">
                                        <li><a href="pages.html">All pages</a></li>
                                        <li><a href="new-page.html">Add new page</a></li>
                                    </ul>
                                </li> --}}
                                {{--                                 <li>
                                    <a href="comments.html">
                                        <span class="icon message" aria-hidden="true"></span>Comments
                                    </a>
                                    <span class="msg-counter">7</span>
                                </li> --}}
                            </ul>
                            <span class="system-menu__title">system</span>
                            <ul class="sidebar-body-menu">
                                {{--                                 <li>
                                    <a href="appearance.html"><span class="icon edit"
                                            aria-hidden="true"></span>Appearance</a>
                                </li> --}}
                                {{--                                 <li>
                                    <a class="show-cat-btn" href="#">
                                        <span class="icon category" aria-hidden="true"></span>Extentions
                                        <span class="category__btn transparent-btn" title="Open list">
                                            <span class="sr-only">Open list</span>
                                            <span class="icon arrow-down" aria-hidden="true"></span>
                                        </span>
                                    </a>
                                    <ul class="cat-sub-menu">
                                        <li><a href="extention-01.html">Extentions-01</a></li>
                                        <li><a href="extention-02.html">Extentions-02</a></li>
                                    </ul>
                                </li> --}}
                                <li>
                                    <a class="show-cat-btn" href="#">
                                        <span class="icon user-3" aria-hidden="true"></span>Users
                                        <span class="category__btn transparent-btn" title="Open list">
                                            <span class="sr-only">Open list</span>
                                            <span class="icon arrow-down" aria-hidden="true"></span>
                                        </span>
                                    </a>
                                    <ul class="cat-sub-menu">
                                        <li><a href="users-01.html">Users-01</a></li>
                                        <li><a href="users-02.html">Users-02</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#"><span class="icon setting" aria-hidden="true"></span>Settings</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="sidebar-footer">
                        <a href="#" class="sidebar-user">
                            <span class="sidebar-user-img">
                                <picture>
                                    <source srcset="./img/avatar/avatar-illustrated-01.webp" type="image/webp">
                                    <img src="./img/avatar/avatar-illustrated-01.png" alt="User name">
                                </picture>
                            </span>
                            <div class="sidebar-user-info">
                                <span class="sidebar-user__title">Nafisa Sh.</span>
                                <span class="sidebar-user__subtitle">Support manager</span>
                            </div>
                        </a>
                    </div>
                </aside>
                <div class="main-wrapper">
                    <!-- ! Main nav -->
                    <nav class="main-nav--bg">
                        <div class="container main-nav">
                            <div class="main-nav-start">
                                <div class="search-wrapper">
                                    <i data-feather="search" aria-hidden="true"></i>
                                    <input type="text" placeholder="Enter keywords ..." required>
                                </div>
                            </div>
                            <div class="main-nav-end">
                                <button class="sidebar-toggle transparent-btn" title="Menu" type="button">
                                    <span class="sr-only">Toggle menu</span>
                                    <span class="icon menu-toggle--gray" aria-hidden="true"></span>
                                </button>
                                {{--                              <div class="lang-switcher-wrapper">
                                    <button class="lang-switcher transparent-btn" type="button">
                                        EN
                                        <i data-feather="chevron-down" aria-hidden="true"></i>
                                    </button>
                                    <ul class="lang-menu dropdown">
                                        <li><a href="##">English</a></li>
                                        <li><a href="##">French</a></li>
                                        <li><a href="##">Uzbek</a></li>
                                    </ul>
                                </div> --}}
                                <button class="theme-switcher gray-circle-btn" type="button" title="Switch theme">
                                    <span class="sr-only">Switch theme</span>
                                    <i class="sun-icon" data-feather="sun" aria-hidden="true"></i>
                                    <i class="moon-icon" data-feather="moon" aria-hidden="true"></i>
                                </button>
                                <div class="notification-wrapper">
                                    <button class="gray-circle-btn dropdown-btn" title="To messages" type="button">
                                        <span class="sr-only">To messages</span>
                                        <span class="icon notification active" aria-hidden="true"></span>
                                    </button>
                                    <ul class="users-item-dropdown notification-dropdown dropdown">
                                        <li>
                                            <a href="##">
                                                <div class="notification-dropdown-icon info">
                                                    <i data-feather="check"></i>
                                                </div>
                                                <div class="notification-dropdown-text">
                                                    <span class="notification-dropdown__title">System just updated</span>
                                                    <span class="notification-dropdown__subtitle">The system has been
                                                        successfully upgraded. Read more
                                                        here.</span>
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
                                                        up a lot of memory space and
                                                        interfere ...</span>
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
                                    <button href="##" class="nav-user-btn dropdown-btn" title="My profile"
                                        type="button">
                                        <span class="sr-only">My profile</span>
                                        <span class="nav-user-img">
                                            <picture>
                                                <source srcset="./img/avatar/avatar-illustrated-02.webp"
                                                    type="image/webp"><img src="./img/avatar/avatar-illustrated-02.png"
                                                    alt="User name">
                                            </picture>
                                        </span>
                                    </button>
                                    <ul class="users-item-dropdown nav-user-dropdown dropdown">
                                        <li><a href="##">
                                                <i data-feather="user" aria-hidden="true"></i>
                                                <span>Profile</span>
                                            </a></li>
                                        <li><a href="##">
                                                <i data-feather="settings" aria-hidden="true"></i>
                                                <span>Account settings</span>
                                            </a></li>
                                        <li><a class="danger" href="##">
                                                <i data-feather="log-out" aria-hidden="true"></i>
                                                <span>Log out</span>
                                            </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </nav>



                    <!-- Main Content -->
                    <main id="main-content">
                        @yield('content')
                    </main>
                    <!-- ! Footer -->
                    <footer class="footer">
                        <div class="container footer--flex">
                            <div class="footer-start">
                                <p><span id="current-year"></span> © K3G Solutions - <a href="k3gsolutions.com.br"
                                        target="_blank" rel="noopener noreferrer">k3gsolutions.com.br</a></p>
                            </div>
                            <ul class="footer-end">
                                <li><a href="##">About</a></li>
                                <li><a href="##">Support</a></li>
                                <li><a href="##">Puchase</a></li>
                            </ul>
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
    <script src="plugins/chart.min.js"></script>
    <script src="plugins/feather.min.js"></script>
    <script src="js/script.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const yearSpan = document.getElementById('current-year');
            const currentYear = new Date().getFullYear();
            yearSpan.textContent = currentYear;
        });
    </script>

</body>

</html>
