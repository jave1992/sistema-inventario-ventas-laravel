<div>
    <h2 class="text-xl font-bold dark:text-white">Apariencia</h2>
    <p class="text-sm text-gray-500 mt-1 mb-6">
        Personaliza la apariencia del sistema.
    </p>

    <form action="{{ route('admin.settings.appearance.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            <div>
                <label class="block font-medium text-sm mb-3">Tema del sistema</label>

                <select name="theme"
                    class="w-full rounded-xl border-gray-300 dark:bg-gray-800 dark:text-white">
                    @foreach(['light' => 'Claro', 'dark' => 'Oscuro', 'system' => 'Automático'] as $value => $label)
                    <option value="{{ $value }}"
                        @selected(old('theme', $settings['theme'] ?? 'light' )===$value)>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium text-sm mb-3">Color principal</label>

                <select name="primary_color"
                    class="w-full rounded-xl border-gray-300 dark:bg-gray-800 dark:text-white">
                    @foreach(['blue' => 'Azul', 'indigo' => 'Índigo', 'emerald' => 'Verde', 'violet' => 'Violeta', 'rose' => 'Rosa'] as $value => $label)
                    <option value="{{ $value }}"
                        @selected(old('primary_color', $settings['primary_color'] ?? 'blue' )===$value)>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>

            <button class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold">
                Guardar apariencia
            </button>

        </div>
    </form>
</div>