<x-admin-layout title="Facturas" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Facturas',
    ],
]">
    @livewire('admin.datatables.invoice-table')
</x-admin-layout>
