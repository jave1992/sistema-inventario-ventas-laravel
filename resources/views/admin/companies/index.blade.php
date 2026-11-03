<x-admin-layout title="Compañías" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Compañías',
    ],
]">
    @push('css')
        <style>
            table th span,
            table td {
                font-size: 0.75rem !important;
            }
        </style>
    @endpush

    @can('create-companies')
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.companies.import') }}" green>
            <i class="fas fa-file-import"></i>
            Importar
        </x-wire-button>
        <x-wire-button href="{{ route('admin.companies.create') }}" blue>
            <i class="fas fa-plus"></i>
            Nuevo
        </x-wire-button>
    </x-slot>
    @endcan

    @livewire('admin.datatables.company-table')
</x-admin-layout>
