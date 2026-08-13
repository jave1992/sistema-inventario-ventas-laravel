<?php

namespace App\Livewire\Admin\Datatables;

use App\Exports\PurchaseOrdersExport;
use App\Mail\PdfSend;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;

class PurchaseOrderTable extends DataTableComponent
{
    // protected $model = PurchaseOrder::class;

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
                return view('admin.purchase_orders.actions', ['purchaseOrder' => $row]);
            })
        ];
    }

    public function builder(): Builder
    {
        return PurchaseOrder::query()->with(['supplier']);
    }

    // Propiedades

    public $form = [
        'open' => false,
        'document' => '',
        'client' => '',
        'email' => '',
        'model' => null,
        'view_pdf_patch' => 'admin.purchase_orders.pdf',
    ];

    // Métodos

    public function openModal(PurchaseOrder $purchaseOrder)
    {
        $this->form['open'] = true;
        $this->form['document'] = 'Orden de Compra ' . $purchaseOrder->serie . ' - ' . $purchaseOrder->correlative;
        $this->form['client'] = $purchaseOrder->supplier->document_number . ' - ' . $purchaseOrder->supplier->name;
        $this->form['email'] = $purchaseOrder->supplier->email;
        $this->form['model'] = $purchaseOrder;
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

    public function bulkActions(): array
    {
        return [
            'exportSelected' => 'Exportar',
        ];
    }

    public function exportSelected()
    {
        $selected = $this->getSelected();

        $purchaseOrders = count($selected) ? PurchaseOrder::whereIn('id', $selected)->with(['supplier.identity'])->get() : PurchaseOrder::with(['supplier.identity'])->get();
        
        return Excel::download(new PurchaseOrdersExport($purchaseOrders), 'ordenes_de_compra.xlsx');
    }
}
