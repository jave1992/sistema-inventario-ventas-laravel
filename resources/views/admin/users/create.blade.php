<x-admin-layout title="Usuarios" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Usuarios',
        'href' => route('admin.users.index'),
    ],
    [
        'name' => 'Nuevo',
    ],
]">
    <x-wire-card>
        <h1 class="text-2xl font-semibold mb-4">Nuevo Usuario</h1>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <x-wire-input label="Nombre" name="name" placeholder="Nombre del usuario" value="{{ old('name') }}" required />
                <x-wire-input type="email" label="Correo Electrónico" name="email" placeholder="Correo electrónico del usuario" value="{{ old('email') }}" required />
                <x-wire-input type="password" label="Contraseña" name="password" placeholder="Contraseña del usuario" required />
                <x-wire-input type="password" label="Confirmar Contraseña" name="password_confirmation" placeholder="Confirma la contraseña del usuario" required />
            </div>
            <div class="flex justify-end mt-4">
                <x-wire-button type="submit" blue>Crear Usuario</x-wire-button>
            </div>
        </form>
    </x-wire-card>
</x-admin-layout>