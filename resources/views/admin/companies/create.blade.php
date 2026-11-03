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
        'name' => 'Nuevo',
        'href' => route('admin.companies.create'),
    ],
]">
    <x-wire-card>
        <form action="{{ route('admin.companies.store') }}" method="POST" class="space-y-4">
            @csrf

            <x-wire-input label="Razón Social" name="razon_social" placeholder="Razón social de la compañía" value="{{ old('razon_social') }}" />

            <x-wire-input label="RUC" name="ruc" placeholder="Número de RUC" value="{{ old('ruc') }}" />

            <x-wire-input label="Dirección" name="direccion" placeholder="Dirección de la compañía" value="{{ old('direccion') }}" />
            
            <x-wire-input label="Usuario SOL" name="sol_user" placeholder="Usuario SOL" value="{{ old('sol_user') }}" />
            
            <x-wire-input type="password" label="Contraseña SOL" name="sol_pass" placeholder="Contraseña SOL" value="{{ old('sol_pass') }}" />
            
            <x-wire-input label="Credencial ID" name="client_id" placeholder="Credencial ID" value="{{ old('client_id') }}" />
            
            <x-wire-input label="Credencial Secret" name="client_secret" placeholder="Credencial Secret" value="{{ old('client_secret') }}" />

            <x-wire-native-select label="Produccion" name="production">
                <option value="0" @selected(old('production')==0)>No</option>
                <option value="1" @selected(old('production')==1)>Si</option>
            </x-wire-native-select>

            <div class="flex justify-end">
                <x-button>
                    Guardar
                </x-button>
            </div>
        </form>
    </x-wire-card>
</x-admin-layout>