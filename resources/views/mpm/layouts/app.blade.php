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
    </style>
</head>

<body class="bg-gray-200">

    @include('mpm/layouts/nav')

    <div class="main-content">
        @yield('content')
    </div>

    @include('mpm/components/goToTop')

    <script src="{{ asset('asset/js/goToTop.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>


    @stack('scripts')

</body>

</html>
