<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // =====================================
        // 1. INDICADORES PRINCIPALES
        // =====================================

        $totalProductos = Product::count('*');

        $totalUnidades = Product::sum('stock');

        $productosStockBajo = Product::whereColumn('stock', '<=', 'min_stock')
            ->where('stock', '>', 0)
            ->count('*');

        $productosSinStock = Product::where('stock', 0)
            ->count('*');

        $valorInventario = Product::selectRaw('COALESCE(SUM(stock * price), 0) as total')
            ->value('total');

        $totalCategorias = Category::count('*');

        // =====================================
        // 2. PRODUCTOS CON MAYOR STOCK
        // =====================================

        $topProductos = Product::orderByDesc('stock')
            ->limit(10)
            ->get(['name', 'stock']);

        $topProductosLabels = $topProductos
            ->pluck('name')
            ->values();

        $topProductosData = $topProductos
            ->pluck('stock')
            ->values();

        // =====================================
        // 3. DISTRIBUCIÓN DEL INVENTARIO
        // =====================================

        $stockNormal = Product::where('stock', '>', DB::raw('min_stock'))
            ->count('*');

        $stockBajo = Product::where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'min_stock')
            ->count('*');

        $sinStock = $productosSinStock;

        $distribucionLabels = [
            'Stock normal',
            'Stock bajo',
            'Sin stock'
        ];

        $distribucionData = [
            $stockNormal,
            $stockBajo,
            $sinStock
        ];

        // =====================================
        // 4. PRODUCTOS QUE NECESITAN REPOSICIÓN
        // =====================================

        $productosCriticos = Product::with('category')
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalProductos',
            'totalUnidades',
            'productosStockBajo',
            'productosSinStock',
            'valorInventario',
            'totalCategorias',
            'topProductosLabels',
            'topProductosData',
            'distribucionLabels',
            'distribucionData',
            'productosCriticos',
        ));
    }
}
