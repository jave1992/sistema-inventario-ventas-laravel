<?php

namespace App\Livewire\Admin;

use App\Exports\CustomersTemplateExport;
use App\Imports\CustomersImport;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ImportOfCustomers extends Component
{
    use WithFileUploads;

    public $file;

    public $errors = [];
    public $importedCount = 0;

    public function render()
    {
        return view('livewire.admin.import-of-customers');
    }

    public function downloadTemplate()
    {
        return Excel::download(new CustomersTemplateExport, 'plantilla_clientes.xlsx');
    }

    public function importCustomers()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $customersImport = new CustomersImport();

        Excel::import($customersImport, $this->file);

        $this->errors = $customersImport->getErrors();
        $this->importedCount = $customersImport->getImportedCount();

        if (count($this->errors) == 0) {
            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Importación exitosa',
                'text' => "Se han importado {$this->importedCount} clientes correctamente.",
            ]);

            return redirect()->route('admin.customers.index');
        }
    }
}
