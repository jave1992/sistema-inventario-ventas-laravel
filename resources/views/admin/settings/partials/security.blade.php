<div>
    <h2 class="text-xl font-bold dark:text-white">Seguridad</h2>
    <p class="text-sm text-gray-500 mt-1 mb-6">
        Mantén protegida tu cuenta.
    </p>

    <form action="{{ route('admin.settings.security.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-5">

            <div>
                <label class="block text-sm font-medium mb-2">Contraseña actual</label>
                <input type="password" name="current_password" required
                    class="w-full rounded-xl border-gray-300 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Nueva contraseña</label>
                <input type="password" name="password" required
                    class="w-full rounded-xl border-gray-300 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" required
                    class="w-full rounded-xl border-gray-300 dark:bg-gray-800 dark:text-white">
            </div>

            <button class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold">
                Cambiar contraseña
            </button>

        </div>
    </form>
</div>