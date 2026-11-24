<x-admin-layout title="Notas de Crédito" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Notas de Crédito',
    ],
]">
    @livewire('admin.datatables.note-table')
</x-admin-layout>
