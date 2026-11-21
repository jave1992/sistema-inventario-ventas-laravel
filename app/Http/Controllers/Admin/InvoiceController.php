<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\SunatService;
use App\Traits\SunatTrait;
use Greenter\Report\XmlUtils;

class InvoiceController extends Controller
{
    use SunatTrait;

    public function index()
    {
        return view('admin.invoices.index');
    }

    public function send(Invoice $invoice)
    {
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

        $sunat->saveInvoiceXml($company, $invoice, $see);

        $response = $sunat->sunatResponse($result);

        $sunat->saveInvoiceCdr($company, $invoice, $result);

        $response['hash'] = (new XmlUtils)->getHashSign($see->getFactory()->getLastXml());

        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Éxito!',
            'text' => $response['cdrResponse']['description']
        ]);

        return redirect()->route('admin.sunat.invoices');
    }
}
