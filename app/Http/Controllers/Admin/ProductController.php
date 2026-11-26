<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        Gate::authorize('read-products');
        
        return view('admin.products.index');
    }

    public function create()
    {
        Gate::authorize('create-products');

        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create-products');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'min_stock' => 'required|integer|min:0'
        ]);

        $product = Product::create($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Bien hecho!',
            'text' => 'El producto se ha creado correctamente'
        ]);

        return view('admin.products.edit', compact('product'));
    }

    public function edit(Product $product)
    {
        Gate::authorize('update-products');

        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        Gate::authorize('update-products');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'min_stock' => 'required|integer|min:0'
        ]);

        $product->update($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Bien hecho!',
            'text' => 'El producto se ha actualizado correctamente'
        ]);

        return redirect()->route('admin.products.edit', $product);
    }

    public function destroy(Product $product)
    {
        Gate::authorize('delete-products');

        if ($product->inventories()->exists()) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se puede eliminar el producto porque tiene inventarios asociados.'
            ]);

            return redirect()->route('admin.products.index');
        }

        if ($product->purchaseOrders()->exists() || $product->quotes()->exists()) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se puede eliminar el producto porque tiene órdenes de compra o cotizaciones asociadas.'
            ]);

            return redirect()->route('admin.products.index');
        }

        $product->delete($product->id);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Bien hecho!',
            'text' => 'El producto se ha eliminado correctamente'
        ]);

        return redirect()->route('admin.products.index');
    }

    public function dropzone(Request $request, Product $product)
    {
        Gate::authorize('update-products');

        $image = $product->images()->create([
            'path' => Storage::put('/images', $request->file('file')),
            'size' => $request->file('file')->getSize()
        ]);

        return response()->json([
            'id' => $image->id,
            'path' => $image->path
        ]);
    }

    public function kardex(Product $product)
    {
        Gate::authorize('read-products');

        return view('admin.products.kardex', compact('product'));
    }

    public function import()
    {
        Gate::authorize('create-products');

        return view('admin.products.import');
    }
}
