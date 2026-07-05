<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\Movement;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;

class MovementTable extends DataTableComponent
{
    // protected $model = Movement::class;

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
            Column::make("Tipo", "type")
                ->sortable()
                ->format(function ($value) {
                    return match ($value) {
                        1 => 'Ingreso',
                        2 => 'Salida',
                        default => 'Desconocido',
                    };
                }),
            Column::make("Serie", "serie")
                ->sortable(),
            Column::make("Correlativo", "correlative")
                ->sortable(),
            Column::make("Almacén", "warehouse.name")
                ->sortable(),
            Column::make("Motivo", "reason.name")
                ->sortable(),
            Column::make("Total", "total")
                ->sortable()
                ->format(function ($value) {
                    return 'S/ ' . number_format($value, 2, '.', ',');
                }),
            Column::make("Acciones")->label(function ($row) {
                return view('admin.movements.actions', ['movement' => $row]);
            })
        ];
    }

    public function builder(): Builder
    {
        return Movement::query()->with(['warehouse', 'reason']);
    }
}
