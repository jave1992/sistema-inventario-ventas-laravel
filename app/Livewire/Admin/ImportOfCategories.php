<?php

namespace App\Livewire\Admin;

use App\Exports\CategoriesTemplateExport;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ImportOfCategories extends Component
{
    use WithFileUploads;

    public $file;

    public $errors = [];
    public $importedCount = 0;

    public function render()
    {
        return view('livewire.admin.import-of-categories');
    }

    public function downloadTemplate()
    {
        return Excel::download(new CategoriesTemplateExport, 'plantilla_categorias.xlsx');
    }
}
