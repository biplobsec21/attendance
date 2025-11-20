<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Flying Robot')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('asset/css/goToTop.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    {{-- In your app.blade.php or layout file --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: #f0f0f0;
            font-family: 'Inter', sans-serif;
        }

        .main-content {
            position: relative;
            z-index: 1;
            overflow-y: auto;
            height: 100%;
            padding-top: 5rem;
            /* Adjust padding for fixed nav */
        }

        .formBack {
            background-color: transparent;
        }

        /* loader css */
        #ajaxLoaderOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.442);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader-container {
            display: flex;
            gap: 15px;
        }

        .dot {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #003d76;
            animation: bounce 1.2s infinite ease-in-out;
        }

        .dot1 {
            animation-delay: 0s;
        }

        .dot2 {
            animation-delay: 0.2s;
        }

        .dot3 {
            animation-delay: 0.4s;
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: scale(0.8);
                opacity: 0.5;
            }

            40% {
                transform: scale(1.2);
                opacity: 1;
            }
        }

        /* end loader css */
    </style>

    @stack('styles')

</head>

<body class="bg-gray-200">
    <!-- Global AJAX Loader -->
    <div id="ajaxLoaderOverlay" style="display: none;">
        <div class="loader-container">
            <div class="dot dot1"></div>
            <div class="dot dot2"></div>
            <div class="dot dot3"></div>
        </div>
    </div>

    @include('mpm/layouts/nav')

    <div class="main-content">
        @yield('content')
    </div>
    @yield('modals')

    @include('mpm/components/goToTop')

    <script src="{{ asset('asset/js/goToTop.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>


    @stack('scripts')

</body>

</html>
