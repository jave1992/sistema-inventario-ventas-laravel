<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class LowStockTable extends DataTableComponent
{
    protected $model = Product::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Producto", "name")
                ->sortable(),
            Column::make("Stock Actual", "stock")
                ->sortable(),
            Column::make("Stock Mínimo", "min_stock")
                ->sortable(),
            Column::make("Faltante")
                ->label(function ($row) {
                    return $row->min_stock - $row->stock;
                })
                ->sortable(),
        ];
    }

    public function builder(): Builder
    {
        return Product::query()->whereColumn('stock', '<=', 'min_stock');
    }
}
