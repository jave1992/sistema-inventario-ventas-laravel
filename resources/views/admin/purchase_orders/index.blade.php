<x-admin-layout title="Ordenes de compra" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Ordenes de compra',
    ],
]">
    @can('create-purchase-orders')
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.purchase-orders.create') }}" blue>
            Nuevo
        </x-wire-button>
    </x-slot>
    @endcan

    @livewire('admin.datatables.purchase-order-table')
</x-admin-layout>
