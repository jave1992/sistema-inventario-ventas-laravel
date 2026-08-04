<?php

namespace App\Livewire\Admin\Datatables;

use App\Mail\PdfSend;
use App\Models\Transfer;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;

class TransferTable extends DataTableComponent
{
    // protected $model = Transfer::class;

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
            Column::make("Serie", "serie")
                ->sortable(),
            Column::make("Correlativo", "correlative")
                ->sortable(),
            Column::make("Almacén de Origen", "originWarehouse.name")
                ->sortable(),
            Column::make("Almacén de Destino", "destinationWarehouse.name")
                ->sortable(),
            Column::make("Total", "total")
                ->sortable()
                ->format(function ($value) {
                    return 'S/ ' . number_format($value, 2, '.', ',');
                }),
            Column::make("Acciones")->label(function ($row) {
                return view('admin.transfers.actions', ['transfer' => $row]);
            })
        ];
    }

    public function builder(): Builder
    {
        return Transfer::query()->with(['originWarehouse', 'destinationWarehouse']);
    }

    // Propiedades

    public $form = [
        'open' => false,
        'document' => '',
        'client' => '',
        'email' => '',
        'model' => null,
        'view_pdf_patch' => 'admin.transfers.pdf',
    ];

    // Métodos

    public function openModal(Transfer $transfer)
    {
        $this->form['open'] = true;
        $this->form['document'] = 'Transferencia ' . $transfer->serie . ' - ' . $transfer->correlative;
        $this->form['client'] = $transfer->originWarehouse->name;
        $this->form['email'] = '';
        $this->form['model'] = $transfer;
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
