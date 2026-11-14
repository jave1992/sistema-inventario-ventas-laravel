<x-admin-layout title="Compañías" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Compañías',
        'href' => route('admin.companies.index'),
    ],
    [
        'name' => 'Importación',
    ],
]">
    @livewire('admin.import-of-companies')
</x-admin-layout>
