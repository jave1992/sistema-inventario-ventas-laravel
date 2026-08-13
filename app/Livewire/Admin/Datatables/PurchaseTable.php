<?php

namespace App\Livewire\Admin\Datatables;

use App\Exports\PurchasesExport;
use App\Mail\PdfSend;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;

class PurchaseTable extends DataTableComponent
{
    // protected $model = Purchase::class;

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
            MultiSelectFilter::make('Proveedor')
                ->options(
                    Supplier::query()->orderBy('name', 'asc')->get()->keyBy('id')->map(function ($item) {
                        return $item->name;
                    })->toArray()
                )
                ->filter(function ($query, $values) {
                    $query->whereIn('supplier_id', $values);
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
                ->searchable()
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

    // Propiedades

    public $form = [
        'open' => false,
        'document' => '',
        'client' => '',
        'email' => '',
        'model' => null,
        'view_pdf_patch' => 'admin.purchases.pdf',
    ];

    // Métodos

    public function openModal(Purchase $purchase)
    {
        $this->form['open'] = true;
        $this->form['document'] = 'Compra ' . $purchase->serie . ' - ' . $purchase->correlative;
        $this->form['client'] = $purchase->supplier->document_number . ' - ' . $purchase->supplier->name;
        $this->form['email'] = $purchase->supplier->email;
        $this->form['model'] = $purchase;
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

        $purchases = count($selected) ? Purchase::whereIn('id', $selected)->with(['supplier.identity'])->get() : Purchase::with(['supplier.identity'])->get();
        
        return Excel::download(new PurchasesExport($purchases), 'compras.xlsx');
    }
}
