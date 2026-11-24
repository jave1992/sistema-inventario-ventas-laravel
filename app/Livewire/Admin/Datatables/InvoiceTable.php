<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\Invoice;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;

class InvoiceTable extends DataTableComponent
{
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function filters(): array
    {
        return [
            DateRangeFilter::make('Fecha')
                ->config([
                    'placeholder' => 'Seleccione un rango de fechas',
                ])
                ->filter(function ($query, array $dateRange) {
                    $query->whereBetween('fecha_envio', [$dateRange['minDate'] . ' 00:00:00', $dateRange['maxDate'] . ' 23:59:59']);
                }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Serie", "serie")
                ->sortable(),
            Column::make("Correlativo", "correlativo")
                ->sortable(),
            Column::make("Razon social", "razon_social")
                ->sortable(),
            Column::make("Documento", "ruc")
                ->sortable(),
            Column::make("Total", "total")
                ->sortable()
                ->format(function ($value) {
                    return 'S/ ' . number_format($value, 2, '.', ',');
                }),
            Column::make("Fecha de Envío", "fecha_envio")
                ->sortable()
                ->format(function ($value) {
                    return $value ? $value->format('d-m-Y') : null;
                }),
            Column::make("Estado", "estado")
                ->sortable()
                ->format(function ($value) {
                    if ($value == 1) {
                        return '<span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Aceptado</span>';
                    } elseif ($value == 2) {
                        return '<span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Rechazado</span>';
                    } else {
                        return '<span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">Pendiente</span>';
                    }
                })
                ->html(),
            Column::make("Acciones")->label(function ($row) {
                return view('admin.invoices.actions', ['invoice' => $row]);
            })
        ];
    }

    public function builder(): Builder
    {
        return Invoice::query();
    }
}
