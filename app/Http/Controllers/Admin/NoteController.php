<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Note;
use App\Services\SunatService;
use App\Traits\SunatTrait;
use Greenter\Report\XmlUtils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    use SunatTrait;

    public function index()
    {
        return view('admin.notes.index');
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

                return redirect()->route('admin.sunat.notes');
            }

            if (empty($company->image)) {
                session()->flash('swal', [
                    'icon' => 'error',
                    'title' => '¡Error!',
                    'text' => 'La compañía no tiene logo'
                ]);

                return redirect()->route('admin.sunat.notes');
            }

            $data = [
                'ublVersion' => '2.1',
                'tipoDoc' => '07',
                'serie' => $invoice->serie,
                'correlativo' => $invoice->correlativo,
                'fechaEmision' => $invoice->sale->date->format('Y-m-d'),
                'tipDocAfectado' => '01',
                'numDocFectado' => $invoice->serie . '-' . $invoice->correlativo,
                'codMotivo' => '01',
                'desMotivo' => 'ANULACION DE LA OPERACION',
                'formaPago' => [
                    'moneda' => 'PEN',
                    'tipo' => 'Contado'
                ],
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
            $note = $sunat->getNote($data);

            $result = $see->send($note);

            $path_xml = $sunat->saveNoteXml($company, $note, $see);

            $response = $sunat->sunatResponse($result);

            $path_cdr = $sunat->saveNoteCdr($company, $note, $result);

            $response = $sunat->sunatResponse($result);

            $response['hash'] = (new XmlUtils)->getHashSign($see->getFactory()->getLastXml());

            $path_pdf = $sunat->generatePdfReport($note, $response['hash']);

            if ($response['success'] == true) {
                session()->flash('swal', [
                    'icon' => 'success',
                    'title' => '¡Éxito!',
                    'text' => $response['cdrResponse']['description']
                ]);

                $note = Note::create([
                    'serie' => $invoice_tmp->serie,
                    'correlativo' => $invoice_tmp->correlativo,
                    'razon_social' => $invoice_tmp->razon_social,
                    'ruc' => $invoice_tmp->ruc,
                    'total' => $invoice_tmp->total,
                    'hash' => $response['hash'],
                    'fecha_envio' => now(),
                    'estado' => 2,
                    'path_pdf' => $path_pdf,
                    'path_xml' => $path_xml,
                    'path_cdr' => $path_cdr,
                    'invoice_id' => $invoice_tmp->id
                ]);

                $invoice_tmp->update(['estado' => 2]);
            } else {
                session()->flash('swal', [
                    'icon' => 'error',
                    'title' => '¡Error!',
                    'text' => $response['error']['message']
                ]);
            }
        });

        return redirect()->route('admin.sunat.notes');
    }

    public function pdf(Note $note)
    {
        /* return Storage::response(
            $note->path_pdf,
            'note.pdf',
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline',
            ]
        ); */

        return Storage::download($note->path_pdf, 'note.pdf');
    }

    public function xml(Note $note)
    {
        return Storage::download($note->path_xml, 'note.xml');
    }

    public function cdr(Note $note)
    {
        return Storage::download($note->path_cdr, 'note.zip');
    }
}
