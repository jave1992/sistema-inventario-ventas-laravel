<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WarehouseController extends Controller
{
    public function index()
    {
        Gate::authorize('read-warehouses');
        
        return view('admin.warehouses.index');
    }

    public function create()
    {
        Gate::authorize('create-warehouses');

        return view('admin.warehouses.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create-warehouses');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255'
        ]);

        $warehouse = Warehouse::create($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Bien hecho!',
            'text' => 'El almacén se ha creado correctamente.'
        ]);

        return redirect()->route('admin.warehouses.edit', $warehouse);
    }

    public function edit(Warehouse $warehouse)
    {
        Gate::authorize('update-warehouses');

        return view('admin.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        Gate::authorize('update-warehouses');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255'
        ]);

        $warehouse->update($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Bien hecho!',
            'text' => 'El almacén se ha actualizado correctamente.'
        ]);

        return redirect()->route('admin.warehouses.edit', $warehouse);
    }

    public function destroy(Warehouse $warehouse)
    {
        Gate::authorize('delete-warehouses');

        if ($warehouse->inventories()->exists()) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => '¡Error!',
                'text' => 'No se puede eliminar el almacén porque tiene inventarios asociados.'
            ]);

            return redirect()->route('admin.warehouses.index');
        }

        $warehouse->delete($warehouse->id);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Bien hecho!',
            'text' => 'El almacén se ha eliminado correctamente.'
        ]);

        return redirect()->route('admin.warehouses.index');
    }

    public function import()
    {
        Gate::authorize('create-warehouses');

        return view('admin.warehouses.import');
    }
}
