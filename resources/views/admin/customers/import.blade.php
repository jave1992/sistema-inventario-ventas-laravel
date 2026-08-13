<x-admin-layout title="Clientes" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Clientes',
        'href' => route('admin.customers.index'),
    ],
    [
        'name' => 'Importación',
    ],
]">
    @livewire('admin.import-of-customers')
</x-admin-layout>
