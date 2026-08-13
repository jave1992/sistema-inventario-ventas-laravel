<x-admin-layout title="Proveedores" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Proveedores',
        'href' => route('admin.suppliers.index'),
    ],
    [
        'name' => 'Importación',
    ],
]">
    @livewire('admin.import-of-suppliers')
</x-admin-layout>
