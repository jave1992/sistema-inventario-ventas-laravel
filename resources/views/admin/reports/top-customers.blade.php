<x-admin-layout title="Reportes" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Clientes más frecuentes',
    ],
]">
    @livewire('admin.datatables.top-customers-table')
</x-admin-layout>