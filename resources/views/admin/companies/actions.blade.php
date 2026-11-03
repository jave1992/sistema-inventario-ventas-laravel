<div class="flex items-center space-x-2">
    @can('update-companies')
    <x-wire-button href="{{ route('admin.companies.edit', $company) }}" blue xs>
        Editar
    </x-wire-button>
    @endcan

    @can('delete-companies')
    <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" class="delete-form">
        @csrf
        @method('DELETE')

        <x-wire-button type="submit" red xs>
            Eliminar
        </x-wire-button>
    </form>
    @endcan
</div>