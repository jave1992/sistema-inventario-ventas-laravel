<div class="flex items-center space-x-4">
    @can('read-invoices')
    @if(in_array($invoice->estado, [0, 2]))
    <x-wire-button blue href="{{ route('admin.sunat.invoices.send', $invoice) }}">
        <i class="fa-solid fa-paper-plane"></i> Enviar
    </x-wire-button>
    @endif
    @if($invoice->estado == 1)
    <x-wire-button green href="{{ route('admin.sunat.invoices.xml', $invoice) }}">
        <i class="fa-solid fa-file-code"></i> XML
    </x-wire-button>
    <x-wire-button secondary href="{{ route('admin.sunat.invoices.cdr', $invoice) }}">
        <i class="fa-solid fa-file-zipper"></i> CDR
    </x-wire-button>
    <x-wire-button red href="{{ route('admin.sunat.invoices.pdf', $invoice) }}">
        <i class="fa-solid fa-file-pdf"></i> PDF
    </x-wire-button>
    @endif
    @endcan
</div>