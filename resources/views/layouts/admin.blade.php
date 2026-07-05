@props([
    'title' => config('app.name', 'Laravel'),
    'breadcrumbs' => [],
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link href="{{ asset('build/assets/css/fonts-bunny-net.css') }}" rel="stylesheet" />

    <!-- FontAwesome -->
    <script src="{{ asset('build/assets/js/9fe0564c68.js') }}" crossorigin="anonymous"></script>

    <!-- SweetAlert 2 -->
    <script src="{{ asset('build/assets/js/sweetalert2@11.js') }}"></script>

    <!-- wireui -->
    <wireui:scripts />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
    
    @stack('css')
</head>

<body class="font-sans antialiased bg-gray-50">

    @include('layouts.includes.admin.navigation')

    @include('layouts.includes.admin.sidebar')

    <div class="p-4 sm:ml-64">
        <div class="mt-14 flex items-center">
            @include('layouts.includes.admin.breadcrumb')

            @isset($action)
                <div class="ml-auto">
                    {{ $action }}
                </div>
            @endisset
        </div>
        {{ $slot }}
    </div>

    @stack('modals')

    @livewireScripts

    <script src="{{ asset('build/assets/js/flowbite.min.js') }}"></script>

    <script>
        Livewire.on('swal', (data) => {
            Swal.fire(data[0]);
        });
    </script>

    @if (session('swal'))
        <script>
            Swal.fire(@json(session('swal')));
        </script>
    @endif

    @stack('js')
</body>

</html>
