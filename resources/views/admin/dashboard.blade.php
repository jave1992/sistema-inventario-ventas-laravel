<x-admin-layout title="Dashboard" :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard')
        ]
    ]">
    <div class="min-h-screen flex">

        <!-- CONTENIDO PRINCIPAL -->
        <main class="flex-1 min-w-0">

            <!-- HEADER -->
            <header class="bg-white shadow-sm px-6 py-5 flex justify-between items-center">

                <div>
                    <h1 class="text-2xl font-bold">
                        Dashboard
                    </h1>

                    <p class="text-sm text-gray-500">
                        Resumen general del inventario
                    </p>
                </div>

                <div class="text-sm text-gray-600">
                    {{ now()->format('d/m/Y') }}
                </div>

            </header>


            <div class="p-6 space-y-6">

                <!-- INDICADORES -->
                <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

                    <!-- PRODUCTOS -->
                    <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-blue-500">

                        <p class="text-gray-500 text-sm">
                            Total de productos
                        </p>

                        <h2 class="text-3xl font-bold mt-2">
                            {{ number_format($totalProductos) }}
                        </h2>

                        <p class="text-sm text-gray-400 mt-2">
                            Productos activos
                        </p>

                    </div>


                    <!-- UNIDADES -->
                    <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500">

                        <p class="text-gray-500 text-sm">
                            Unidades en stock
                        </p>

                        <h2 class="text-3xl font-bold mt-2">
                            {{ number_format($totalUnidades) }}
                        </h2>

                        <p class="text-sm text-gray-400 mt-2">
                            Existencias disponibles
                        </p>

                    </div>


                    <!-- STOCK BAJO -->
                    <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-yellow-500">

                        <p class="text-gray-500 text-sm">
                            Stock bajo
                        </p>

                        <h2 class="text-3xl font-bold mt-2 text-yellow-600">
                            {{ number_format($productosStockBajo) }}
                        </h2>

                        <p class="text-sm text-gray-400 mt-2">
                            Requieren reposición
                        </p>

                    </div>


                    <!-- SIN STOCK -->
                    <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-red-500">

                        <p class="text-gray-500 text-sm">
                            Productos sin stock
                        </p>

                        <h2 class="text-3xl font-bold mt-2 text-red-600">
                            {{ number_format($productosSinStock) }}
                        </h2>

                        <p class="text-sm text-gray-400 mt-2">
                            Existencias agotadas
                        </p>

                    </div>


                    <!-- VALOR -->
                    <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-purple-500">

                        <p class="text-gray-500 text-sm">
                            Valor del inventario
                        </p>

                        <h2 class="text-3xl font-bold mt-2">
                            S/ {{ number_format($valorInventario, 2) }}
                        </h2>

                        <p class="text-sm text-gray-400 mt-2">
                            Valor basado en precio × stock
                        </p>

                    </div>


                    <!-- CATEGORÍAS -->
                    <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-indigo-500">

                        <p class="text-gray-500 text-sm">
                            Categorías
                        </p>

                        <h2 class="text-3xl font-bold mt-2">
                            {{ number_format($totalCategorias) }}
                        </h2>

                        <p class="text-sm text-gray-400 mt-2">
                            Categorías activas
                        </p>

                    </div>

                </section>


                <!-- GRAFICOS PRINCIPALES -->
                <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                    <!-- GRAFICO DE BARRAS -->
                    <div class="bg-white rounded-xl shadow-sm p-6">

                        <h3 class="text-lg font-bold mb-1">
                            Productos con mayor stock
                        </h3>

                        <p class="text-sm text-gray-500 mb-5">
                            Top 10 productos disponibles
                        </p>

                        <div class="relative h-80">
                            <canvas id="productosChart"></canvas>
                        </div>

                    </div>

                </section>

                <!-- GRAFICO DONA Y TABLA -->
                <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                    <!-- DONA -->
                    <div class="bg-white rounded-xl shadow-sm p-6">

                        <h3 class="text-lg font-bold mb-1">
                            Estado del inventario
                        </h3>

                        <p class="text-sm text-gray-500 mb-5">
                            Clasificación por stock
                        </p>

                        <div class="relative h-64">
                            <canvas id="stockChart"></canvas>
                        </div>

                    </div>


                    <!-- PRODUCTOS CRITICOS -->
                    <div class="xl:col-span-2 bg-white rounded-xl shadow-sm p-6">

                        <h3 class="text-lg font-bold mb-1">
                            Productos que necesitan reposición
                        </h3>

                        <p class="text-sm text-gray-500 mb-5">
                            Productos con stock igual o inferior al mínimo
                        </p>

                        <div class="overflow-x-auto">

                            <table class="w-full text-sm text-left">

                                <thead class="bg-gray-50 text-gray-600">
                                    <tr>
                                        <th class="p-3">Producto</th>
                                        <th class="p-3">Categoría</th>
                                        <th class="p-3">Stock</th>
                                        <th class="p-3">Mínimo</th>
                                        <th class="p-3">Estado</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($productosCriticos as $producto)

                                    <tr class="border-b">

                                        <td class="p-3 font-medium">
                                            {{ $producto->name }}
                                        </td>

                                        <td class="p-3">
                                            {{ $producto->category?->name ?? 'Sin categoría' }}
                                        </td>

                                        <td class="p-3 font-bold">
                                            {{ $producto->stock }}
                                        </td>

                                        <td class="p-3">
                                            {{ $producto->minimum_stock }}
                                        </td>

                                        <td class="p-3">

                                            @if($producto->stock == 0)

                                            <span class="px-2 py-1 rounded bg-red-100 text-red-700">
                                                Agotado
                                            </span>

                                            @else

                                            <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-700">
                                                Stock bajo
                                            </span>

                                            @endif

                                        </td>

                                    </tr>

                                    @empty

                                    <tr>
                                        <td colspan="5" class="p-5 text-center text-gray-500">
                                            No hay productos críticos.
                                        </td>
                                    </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </section>

            </div>

        </main>

    </div>

    <!-- CONFIGURACION DE CHART.JS -->
    <script>
        // Datos enviados desde Laravel
        const productosLabels = @json($topProductosLabels);
        const productosData = @json($topProductosData);

        const distribucionLabels = @json($distribucionLabels);
        const distribucionData = @json($distribucionData);


        // OPCIONES GENERALES
        Chart.defaults.font.family = 'Arial';
        Chart.defaults.color = '#6b7280';


        // =====================================
        // GRAFICO DE BARRAS
        // =====================================

        new Chart(document.getElementById('productosChart'), {

            type: 'bar',

            data: {
                labels: productosLabels,

                datasets: [{
                    label: 'Unidades disponibles',

                    data: productosData,

                    backgroundColor: '#3b82f6',

                    borderRadius: 6,

                    maxBarThickness: 45
                }]
            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                indexAxis: 'y',

                plugins: {
                    legend: {
                        display: false
                    }
                },

                scales: {
                    x: {
                        beginAtZero: true
                    }
                }

            }

        });

        // =====================================
        // GRAFICO CIRCULAR / DONA
        // =====================================

        new Chart(document.getElementById('stockChart'), {

            type: 'doughnut',

            data: {

                labels: distribucionLabels,

                datasets: [{

                    data: distribucionData,

                    backgroundColor: [
                        '#22c55e',
                        '#f59e0b',
                        '#ef4444'
                    ],

                    borderWidth: 2,

                    hoverOffset: 8

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '65%',

                plugins: {

                    legend: {
                        position: 'bottom'
                    }

                }

            }

        });
    </script>
</x-admin-layout>