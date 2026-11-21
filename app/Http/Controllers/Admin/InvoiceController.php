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
            'serie' => $sale->series,
            'correlativo' => $sale->correlativo,
            'fechaEmision' => $sale->date->format('Y-m-d'),
            'tipoMoneda' => 'PEN',
            'company' => [
                'ruc' => $company->ruc,
                'razonSocial' => $company->razon_social,
                'nombreComercial' => null,
                'address' => [
                    "ubigueo" => "",
                    "departamento" => "",
                    "provincia" => "",
                    "distrito" => "",
                    "urbanizacion" => "-",
                    "direccion" => "",
                    "codLocal" => ""
                ],
            ],
            'client' => [
                'tipoDoc' => '6',
                'numDoc' => $sale->customer->document_number,
                'rznSocial' => $sale->customer->name,
            ],
        ];

        $data['details'] = $sale->products->map(function ($product) {
            return [
                'tipAfeIgv' => 10,
                'codProducto' => $product->sku,
                'unidad' => 'NIU',
                'descripcion' => $product->description,
                'cantidad' => $product->pivot->quantity,
                'mtoValorUnitario' => $product->price,
                'mtoValorVenta' => $product->price * $product->pivot->quantity,
                'mtoBaseIgv' => $product->price * $product->pivot->quantity,
                'porcentajeIgv' => 18,
                'igv' => 18,
                'totalImpuestos' => 18,
                'mtoPrecioUnitario' => $product->price * 1.18,
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

        return response()->json([
            'result' => $response,
        ], 200);
    }
}
