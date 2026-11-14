<?php

namespace App\Livewire\Admin;

use App\Exports\CompaniesTemplateExport;
use App\Imports\CompaniesImport;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ImportOfCompanies extends Component
{
    use WithFileUploads;

    public $file;

    public $errors = [];
    public $importedCount = 0;

    public function render()
    {
        return view('livewire.admin.import-of-companies');
    }

    public function downloadTemplate()
    {
        return Excel::download(new CompaniesTemplateExport, 'plantilla_companias.xlsx');
    }

    public function importCompanies()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $companiesImport = new CompaniesImport();

        Excel::import($companiesImport, $this->file);

        $this->errors = $companiesImport->getErrors();
        $this->importedCount = $companiesImport->getImportedCount();

        if (count($this->errors) == 0) {
            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Importación exitosa',
                'text' => "Se han importado {$this->importedCount} compañías correctamente.",
            ]);

            return redirect()->route('admin.companies.index');
        }
    }
}
