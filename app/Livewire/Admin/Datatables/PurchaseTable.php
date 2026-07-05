<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Builder;

class PurchaseTable extends DataTableComponent
{
    // protected $model = Purchase::class;

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
            Column::make("Razon social", "supplier.name")
                ->sortable(),
            Column::make("Documento", "supplier.document_number")
                ->sortable(),
            Column::make("Total", "total")
                ->sortable()
                ->format(function ($value) {
                    return 'S/ ' . number_format($value, 2, '.', ',');
                }),
            Column::make("Acciones")->label(function ($row) {
                return view('admin.purchases.actions', ['purchase' => $row]);
            })
        ];
    }

    public function builder(): Builder
    {
        return Purchase::query()->with(['supplier']);
    }
}
