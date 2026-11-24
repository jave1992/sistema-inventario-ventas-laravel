<div class="flex items-center space-x-4">
    @can('read-notes')
    @if($invoice->estado == 1)
    <x-wire-button blue href="{{ route('admin.sunat.notes.send', $invoice) }}">
        <i class="fa-solid fa-paper-plane"></i> Enviar
    </x-wire-button>
    @endif
    @if($invoice->estado == 2)
    <x-wire-button green href="{{ route('admin.sunat.notes.xml', $note) }}">
        <i class="fa-solid fa-file-code"></i> XML
    </x-wire-button>
    <x-wire-button secondary href="{{ route('admin.sunat.notes.cdr', $note) }}">
        <i class="fa-solid fa-file-zipper"></i> CDR
    </x-wire-button>
    <x-wire-button red href="{{ route('admin.sunat.notes.pdf', $note) }}">
        <i class="fa-solid fa-file-pdf"></i> PDF
    </x-wire-button>
    @endif
    @endcan
</div>