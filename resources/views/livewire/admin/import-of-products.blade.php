<div>
    <x-wire-card>
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Importar productos desde Excel</h1>
        <x-wire-button blue wire:click="downloadTemplate">
            <i class="fas fa-file-excel"></i>
            Descargar plantilla
        </x-wire-button>
        <p class="text-sm text-gray-500 mt-1">Completa la plantilla con los datos de tus productos y súbelo aquí.</p>
        <div class="mt-4">
            <x-input type="file" accept=".xlsx, .xls" wire:model="file" />
            <x-input-error for="file" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-wire-button green wire:click="importProducts" wire:loading.attr="disabled" wire:target="file"
                spinner="importProducts">
                <i class="fas fa-upload"></i>
                Importar productos
            </x-wire-button>
        </div>
        @if (count($errors))
            <div class="mt-4">
                <div class="p-4 bg-yellow-100 border border-yellow-300 rounded-md text-yellow-800 mb-3">
                    @if ($importedCount)
                        <i class="fas fa-triangle-exclamation"></i>
                        <strong>Importación completada parcialmente.</strong>
                        <p class="mt-1 text-sm">Algunos productos no se pudieron importar correctamente.</p>
                    @else
                        <i class="fas fa-xmark"></i>
                        <strong>No se importó ningún producto.</strong>
                        <p class="mt-1 text-sm">Todos los productos tienen errores de importación o el archivo es
                            inválido.</p>
                    @endif
                </div>
                <ul class="space-y-2">
                    @foreach ($errors as $error)
                        <li class="p-3 bg-red-50 border border-red-200 rounded-md">
                            <p class="text-red-700 font-semibold">
                                <i class="fas fa-file-pen"></i>
                                Fila {{ $error['row'] }}:
                            </p>
                            <ul class="list-disc list-inside mt-1">
                                @foreach ($error['errors'] as $message)
                                    <li class="text-red-600 text-sm">{{ $message }}</li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </x-wire-card>
</div>
