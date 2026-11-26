<div>
    <h2 class="text-xl font-bold dark:text-white">Respaldo de base de datos</h2>

    <p class="text-sm text-gray-500 mt-1 mb-6">
        Genera copias de seguridad para proteger tu información.
    </p>

    <div class="rounded-xl border border-blue-200 bg-blue-50 dark:bg-blue-950/30 dark:border-blue-900 p-5 mb-6">

        <div class="flex items-center gap-3">
            <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-xl">
                <span class="text-blue-600 text-xl">🗄️</span>
            </div>

            <div>
                <h3 class="font-bold text-gray-900 dark:text-white">
                    Copia de seguridad
                </h3>

                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Genera un respaldo de los datos configurados.
                </p>
            </div>
        </div>

        <form action="{{ route('admin.settings.backup') }}"
            method="POST"
            class="mt-5"
            onsubmit="return confirm('¿Deseas generar una nueva copia de seguridad?')">
            @csrf

            <button type="submit"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold">
                Generar respaldo ahora
            </button>
        </form>

    </div>

    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="font-semibold dark:text-white mb-2">
            Recomendaciones
        </h3>

        <ul class="text-sm text-gray-500 space-y-2">
            <li>• Programa respaldos automáticos.</li>
            <li>• Guarda copias fuera del servidor principal.</li>
            <li>• Comprueba periódicamente que puedas restaurar los respaldos.</li>
        </ul>
    </div>
</div>