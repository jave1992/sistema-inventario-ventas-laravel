<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;

class TopCustomersTable extends DataTableComponent
{
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('total_amount', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make("Id")
                ->label(fn($row) => $row->id)
                ->sortable(function (Builder $query, string $direction) {
                    return $query->orderBy('id', $direction);
                }),
            Column::make("Cliente")
                ->label(fn($row) => $row->name)
                ->sortable(function (Builder $query, string $direction) {
                    return $query->orderBy('name', $direction);
                })
                ->searchable(function (Builder $query, string $term) {
                    return $query->orWhere('name', 'like', '%' . $term . '%');
                }),
            Column::make("Email")
                ->label(fn($row) => $row->email)
                ->sortable(function (Builder $query, string $direction) {
                    return $query->orderBy('email', $direction);
                }),
            Column::make("Tipo de Documento")
                ->label(fn($row) => $row->identity_type)
                ->sortable(function (Builder $query, string $direction) {
                    return $query->orderBy('identity_type', $direction);
                }),
            Column::make("Número de Documento")
                ->label(fn($row) => $row->document_number)
                ->sortable(function (Builder $query, string $direction) {
                    return $query->orderBy('document_number', $direction);
                }),
            Column::make("Ventas Totales")
                ->label(fn($row) => $row->total_sales)
                ->sortable(function (Builder $query, string $direction) {
                    return $query->orderBy('total_sales', $direction);
                }),
            Column::make("Monto Total")
                ->label(fn($row) => 'S/. ' . $row->total_amount)
                ->sortable(function (Builder $query, string $direction) {
                    return $query->orderBy('total_amount', $direction);
                }),
        ];
    }

    public function builder(): Builder
    {
        return Sale::query()
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->join('identities', 'customers.identity_id', '=', 'identities.id')
            ->selectRaw('
                customers.id as id,
                customers.name as name,
                customers.email as email,
                identities.name as identity_type,
                customers.document_number as document_number,
                COUNT(sales.id) as total_sales,
                SUM(sales.total) as total_amount
            ')
            ->groupBy('customers.id', 'customers.name', 'customers.email', 'identities.name', 'customers.document_number');
    }
}
