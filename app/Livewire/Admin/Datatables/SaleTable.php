<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\Sale;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;

class SaleTable extends DataTableComponent
{
    // protected $model = Sale::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Fecha", "date")
                ->sortable()
                ->format(function ($value) {
                    return $value->format('Y-m-d');
                }),
            Column::make("Serie", "serie")
                ->sortable(),
            Column::make("Correlativo", "correlative")
                ->sortable(),
            Column::make("Razon social", "customer.name")
                ->sortable(),
            Column::make("Documento", "customer.document_number")
                ->sortable(),
            Column::make("Total", "total")
                ->sortable()
                ->format(function ($value) {
                    return 'S/ ' . number_format($value, 2, '.', ',');
                }),
            Column::make("Acciones")->label(function ($row) {
                return view('admin.sales.actions', ['sale' => $row]);
            })
        ];
    }

    public function builder(): Builder
    {
        return Sale::query()->with(['customer']);
    }
}
