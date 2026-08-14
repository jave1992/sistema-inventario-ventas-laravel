<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Productable;
use Illuminate\Database\Eloquent\Builder;

class TopProductsTable extends DataTableComponent
{
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('subtotal', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make("Id")
                ->label(fn($row) => $row->id)
                ->sortable(function (Builder $query, string $direction) {
                    return $query->orderBy('id', $direction);
                }),
            Column::make("Producto")
                ->label(fn($row) => $row->name)
                ->sortable(function (Builder $query, string $direction) {
                    return $query->orderBy('name', $direction);
                })
                ->searchable(function (Builder $query, string $term) {
                    return $query->orWhere('name', 'like', '%' . $term . '%');
                }),
            Column::make("Cantidad")
                ->label(fn($row) => $row->quantity)
                ->sortable(function (Builder $query, string $direction) {
                    return $query->orderBy('quantity', $direction);
                }),
            Column::make("Subtotal")
                ->label(fn($row) => 'S/. ' . $row->subtotal)
                ->sortable(function (Builder $query, string $direction) {
                    return $query->orderBy('subtotal', $direction);
                }),
        ];
    }

    public function builder(): Builder
    {
        return Productable::query()
            ->where('productable_type', 'App\Models\Sale')
            ->join('products', 'products.id', '=', 'productables.product_id')
            ->selectRaw('
                products.id as id,
                products.name as name,
                products.description as description,
                SUM(productables.quantity) as quantity,
                SUM(productables.subtotal) as subtotal
            ')
            ->groupBy('products.id', 'products.name', 'products.description');
    }
}
