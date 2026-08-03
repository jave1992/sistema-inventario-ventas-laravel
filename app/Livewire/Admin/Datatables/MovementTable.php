<?php

namespace App\Livewire\Admin\Datatables;

use App\Mail\PdfSend;
use App\Models\Movement;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;

class MovementTable extends DataTableComponent
{
    // protected $model = Movement::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');

        $this->setConfigurableAreas([
            'after-wrapper' => [
                'admin.pdf.modal',
            ],
        ]);
    }

    public function filters(): array
    {
        return [
            DateRangeFilter::make('Fecha')
                ->config([
                    'placeholder' => 'Seleccione un rango de fechas',
                ])
                ->filter(function ($query, array $dateRange) {
                    $query->whereBetween('date', [$dateRange['minDate'] . ' 00:00:00', $dateRange['maxDate'] . ' 23:59:59']);
                }),
        ];
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

    // Propiedades

    public $form = [
        'open' => false,
        'document' => '',
        'client' => '',
        'email' => '',
        'model' => null,
        'view_pdf_patch' => 'admin.movements.pdf',
    ];

    // Métodos

    public function openModal(Movement $movement)
    {
        $this->form['open'] = true;
        $this->form['document'] = 'Movimiento ' . $movement->serie . ' - ' . $movement->correlative;
        $this->form['client'] = $movement->warehouse->name . ' - ' . $movement->reason->name;
        $this->form['email'] = $movement->warehouse->email;
        $this->form['model'] = $movement;
    }

    public function sendEmail()
    {
        $this->validate([
            'form.email' => 'required|email',
        ]);

        Mail::to($this->form['email'])->send(new PdfSend($this->form));

        $this->dispatch('swal', [
            'title' => 'Correo enviado',
            'text' => 'El correo ha sido enviado correctamente.',
            'icon' => 'success',
        ]);

        $this->reset('form');
    }
}
