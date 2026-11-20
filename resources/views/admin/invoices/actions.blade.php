<div class="flex items-center space-x-4">
    @can('read-invoices')
    <x-wire-button blue href="{{ route('admin.invoices.send', $invoice) }}">
        <i class="fa-solid fa-paper-plane"></i>
    </x-wire-button>
    @endcan
</div>
