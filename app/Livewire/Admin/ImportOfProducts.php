<?php

namespace App\Livewire\Admin;

use App\Exports\ProductsTemplateExport;
use App\Imports\ProductsImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ImportOfProducts extends Component
{
    use WithFileUploads;

    public $file;

    public function render()
    {
        return view('livewire.admin.import-of-products');
    }

    public function downloadTemplate()
    {
        return Excel::download(new ProductsTemplateExport, 'plantilla_productos.xlsx');
    }

    public function importProducts()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        Excel::import(new ProductsImport, $this->file);
    }
}
