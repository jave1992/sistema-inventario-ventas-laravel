<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Company;

class CompanyTable extends DataTableComponent
{
    protected $model = Company::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Razon social", "razon_social")
                ->sortable(),
            Column::make("Ruc", "ruc")
                ->sortable(),
            Column::make("Direccion", "direccion")
                ->sortable(),
           Column::make("Producción", "production")
                ->sortable()
                ->format(function ($value) {
                    return match ($value) {
                        0 => 'Inactivo',
                        1 => 'Activo',
                        default => 'Desconocido',
                    };
                }),
            Column::make('Acciones')
                ->label(function ($row) {
                    return view('admin.companies.actions', ['company' => $row]);
                })
        ];
    }
}
