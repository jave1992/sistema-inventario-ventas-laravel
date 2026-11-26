<div>
    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
        Configuración general
    </h2>
    <p class="text-sm text-gray-500 mt-1 mb-6">
        Información principal de tu empresa.
    </p>

    <form action="{{ route('admin.settings.general.update') }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-5">

            <div>
                <label class="block text-sm font-medium mb-2">Nombre de la empresa</label>
                <input type="text" name="company_name"
                    value="{{ old('company_name', $settings['company_name'] ?? '') }}"
                    required
                    class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Correo electrónico</label>
                <input type="email" name="email"
                    value="{{ old('email', $settings['email'] ?? '') }}"
                    class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Teléfono</label>
                <input type="text" name="phone"
                    value="{{ old('phone', $settings['phone'] ?? '') }}"
                    class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Dirección</label>
                <input type="text" name="address"
                    value="{{ old('address', $settings['address'] ?? '') }}"
                    class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Logo empresarial</label>

                @if(!empty($settings['company_logo']))
                <img src="{{ asset('storage/'.$settings['company_logo']) }}"
                    class="h-20 w-20 object-contain mb-3 rounded-lg border p-2">
                @endif

                <input type="file" name="logo" accept="image/*"
                    class="block w-full text-sm text-gray-500">
            </div>

            <button type="submit"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold">
                Guardar cambios
            </button>

        </div>
    </form>
</div>