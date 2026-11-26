<x-admin-layout
    title="Ajustes"
    :breadcrumbs="[
        [
            'name' => 'Ajustes',
            'href' => route('admin.settings.index'),
        ]
    ]">
    <div class="space-y-6">

        {{-- Encabezado --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                Configuración del sistema
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Administra las preferencias de tu plataforma.
            </p>
        </div>

        {{-- Mensajes --}}
        @if(session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 p-4 text-green-700">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="rounded-xl bg-red-50 border border-red-200 p-4 text-red-700">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 p-4 text-red-700">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- Menú de ajustes --}}
            <aside class="lg:col-span-3">
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 p-3">

                    @php
                    $menu = [
                    'general' => ['General', 'building-2'],
                    'security' => ['Seguridad', 'shield-check'],
                    'appearance' => ['Apariencia', 'palette'],
                    'backup' => ['Respaldo', 'database'],
                    ];
                    @endphp

                    @foreach($menu as $key => $item)

                    <a href="{{ route('admin.settings.index', ['tab' => $key]) }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 transition
                           {{ $tab === $key
                               ? 'bg-blue-600 text-white'
                               : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">

                        <span class="text-sm font-medium">
                            {{ $item[0] }}
                        </span>

                        @if($tab === $key)
                        <span class="ml-auto">✓</span>
                        @endif

                    </a>

                    @endforeach

                </div>
            </aside>

            {{-- Contenido --}}
            <section class="lg:col-span-9">

                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 md:p-8">

                    @switch($tab)

                    @case('general')
                    @include('admin.settings.partials.general')
                    @break

                    @case('security')
                    @include('admin.settings.partials.security')
                    @break

                    @case('appearance')
                    @include('admin.settings.partials.appearance')
                    @break

                    @case('backup')
                    @include('admin.settings.partials.backup')
                    @break

                    @endswitch
                </div>
            </section>
        </div>
    </div>
</x-admin-layout>