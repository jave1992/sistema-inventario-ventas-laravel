<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\SunatService;
use App\Traits\SunatTrait;
use Greenter\Report\XmlUtils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    use SunatTrait;

    public function index()
    {
        return view('admin.invoices.index');
    }

    public function send(Invoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            $invoice_tmp = $invoice;

            $sale = $invoice->sale;

            $company = $sale->company;

            if (empty($company->cert)) {
                session()->flash('swal', [
                    'icon' => 'error',
                    'title' => '¡Error!',
                    'text' => 'La compañía no tiene certificado digital'
                ]);

                return redirect()->route('admin.sunat.invoices');
            }

            if (empty($company->image)) {
                session()->flash('swal', [
                    'icon' => 'error',
                    'title' => '¡Error!',
                    'text' => 'La compañía no tiene logo'
                ]);

                return redirect()->route('admin.sunat.invoices');
            }

            $data = [
                'ublVersion' => '2.1',
                'tipoOperacion' => '0101',
                'tipoDoc' => '01',
                'serie' => $sale->serie,
                'correlativo' => $sale->correlative,
                'fechaEmision' => $sale->date->format('Y-m-d'),
                'tipoMoneda' => 'PEN',
                'company' => [
                    'ruc' => $company->ruc,
                    'razonSocial' => $company->razon_social,
                    'nombreComercial' => null,
                    'address' => [
                        'ubigueo' => '150101',
                        'departamento' => 'LIMA',
                        'provincia' => 'LIMA',
                        'distrito' => 'LIMA',
                        'urbanizacion' => '-',
                        'direccion' => 'calle falsa 123',
                        'codLocal' => '0000'
                    ],
                ],
                'client' => [
                    'tipoDoc' => '6',
                    'numDoc' => $sale->customer->document_number,
                    'rznSocial' => $sale->customer->name,
                ],
            ];

            $data['details'] = $sale->products->map(function ($product) {
                $valorUnitario = (float) $product->price;

                $baseImponible = round($valorUnitario * $product->pivot->quantity, 2);
                $igv = round($baseImponible * 0.18, 2);
                $precioUnitario = round($valorUnitario * 1.18, 2);

                return [
                    'tipAfeIgv' => 10,
                    'codProducto' => $product->sku,
                    'unidad' => 'NIU',
                    'descripcion' => $product->description,
                    'cantidad' => $product->pivot->quantity,
                    'mtoValorUnitario' => $valorUnitario,
                    'mtoValorVenta' => $baseImponible,
                    'mtoBaseIgv' => $baseImponible,
                    'porcentajeIgv' => 18,
                    'igv' => $igv,
                    'totalImpuestos' => $igv,
                    'mtoPrecioUnitario' => $precioUnitario,
                ];
            });

            $this->setTotales($data);
            $this->setLeyendas($data);

            $sunat = new SunatService();
            $see = $sunat->getSee($company);
            $invoice = $sunat->getInvoice($data);

            $result = $see->send($invoice);

            $path_xml = $sunat->saveInvoiceXml($company, $invoice, $see);

            $response = $sunat->sunatResponse($result);

            $path_cdr = $sunat->saveInvoiceCdr($company, $invoice, $result);

            $response['hash'] = (new XmlUtils)->getHashSign($see->getFactory()->getLastXml());

            $path_pdf = $sunat->generatePdfReport($invoice, $response['hash']);

            if ($response['success'] == true) {
                session()->flash('swal', [
                    'icon' => 'success',
                    'title' => '¡Éxito!',
                    'text' => $response['cdrResponse']['description']
                ]);

                $invoice_tmp->update([
                    'hash' => $response['hash'],
                    'fecha_envio' => now(),
                    'estado' => 1,
                    'path_pdf' => $path_pdf,
                    'path_xml' => $path_xml,
                    'path_cdr' => $path_cdr,
                ]);
            } else {
                session()->flash('swal', [
                    'icon' => 'error',
                    'title' => '¡Error!',
                    'text' => $response['error']['message']
                ]);
            }
        });

        return redirect()->route('admin.sunat.invoices');
    }

    public function pdf(Invoice $invoice)
    {
        /* return Storage::response(
            $invoice->path_pdf,
            'invoice.pdf',
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline',
            ]
        ); */

        return Storage::download($invoice->path_pdf, 'invoice.pdf');
    }

    public function xml(Invoice $invoice)
    {
        return Storage::download($invoice->path_xml, 'invoice.xml');
    }

    public function cdr(Invoice $invoice)
    {
        return Storage::download($invoice->path_cdr, 'invoice.zip');
    }
}
