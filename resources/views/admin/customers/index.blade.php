<x-admin-layout title="Clientes" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Clientes',
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

    <x-slot name="action">
        <x-wire-button href="{{ route('admin.customers.import') }}" green>
            <i class="fas fa-file-import"></i>
            Importar
        </x-wire-button>
        <x-wire-button href="{{ route('admin.customers.create') }}" blue>
            <i class="fas fa-plus"></i>
            Nuevo
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.customer-table')
</x-admin-layout>
