<?php

namespace App\Livewire\Admin;

use App\Exports\ProductsTemplateExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ImportOfProducts extends Component
{
    public function render()
    {
        return view('livewire.admin.import-of-products');
    }

    public function downloadTemplate()
    {
        return Excel::download(new ProductsTemplateExport, 'plantilla_productos.xlsx');
    }
}
