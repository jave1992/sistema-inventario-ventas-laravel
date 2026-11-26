<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Identity;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SupplierController extends Controller
{
    public function index()
    {
        Gate::authorize('read-suppliers');

        return view('admin.suppliers.index');
    }

    public function create()
    {
        Gate::authorize('create-suppliers');
        
        $identities = Identity::all();
        
        return view('admin.suppliers.create', compact('identities'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create-suppliers');

        $data = $request->validate(
            [
                'identity_id' => 'required|exists:identities,id',
                'document_number' => 'required|string|max:20|unique:suppliers,document_number',
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20'
            ]
        );

        $supplier = Supplier::create($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Bien hecho!',
            'text' => 'El proveedor se ha creado correctamente'
        ]);

        return redirect()->route('admin.suppliers.edit', $supplier);
    }

    public function edit(Supplier $supplier)
    {
        Gate::authorize('update-suppliers');

        $identities = Identity::all();

        return view('admin.suppliers.edit', compact('supplier', 'identities'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        Gate::authorize('update-suppliers');
        
        $data = $request->validate(
            [
                'identity_id' => 'required|exists:identities,id',
                'document_number' => 'required|string|max:20|unique:suppliers,document_number,' . $supplier->id,
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20'
            ]
        );

        $supplier->update($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Bien hecho!',
            'text' => 'El proveedor se ha actualizado correctamente.'
        ]);

        return redirect()->route('admin.suppliers.edit', $supplier);
    }

    public function destroy(Supplier $supplier)
    {
        Gate::authorize('delete-suppliers');

        if ($supplier->purchaseOrders()->exists() || $supplier->purchases()->exists()) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se puede eliminar el proveedor porque tiene órdenes de compra o compras asociadas.'
            ]);

            return redirect()->route('admin.suppliers.index');
        }

        $supplier->delete($supplier->id);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Bien hecho!',
            'text' => 'El proveedor se ha eliminado correctamente'
        ]);

        return redirect()->route('admin.suppliers.index');
    }

    public function import()
    {
        Gate::authorize('create-suppliers');

        return view('admin.suppliers.import');
    }
}
