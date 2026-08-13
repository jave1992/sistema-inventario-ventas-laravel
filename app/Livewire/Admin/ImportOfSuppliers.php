<?php

namespace App\Livewire\Admin;

use App\Exports\SuppliersTemplateExport;
use App\Imports\SuppliersImport;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ImportOfSuppliers extends Component
{
    use WithFileUploads;

    public $file;

    public $errors = [];
    public $importedCount = 0;

    public function render()
    {
        return view('livewire.admin.import-of-suppliers');
    }

    public function downloadTemplate()
    {
        return Excel::download(new SuppliersTemplateExport, 'plantilla_proveedores.xlsx');
    }

    public function importSuppliers()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $suppliersImport = new SuppliersImport();

        Excel::import($suppliersImport, $this->file);

        $this->errors = $suppliersImport->getErrors();
        $this->importedCount = $suppliersImport->getImportedCount();

        if (count($this->errors) == 0) {
            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Importación exitosa',
                'text' => "Se han importado {$this->importedCount} proveedores correctamente.",
            ]);

            return redirect()->route('admin.suppliers.index');
        }
    }
}
