<?php

namespace App\Services;

use App\Models\Company as ModelsCompany;
use DateTime;
use Greenter\Api;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Despatch\Despatch;
use Greenter\Model\Despatch\DespatchDetail;
use Greenter\Model\Despatch\Direction;
use Greenter\Model\Despatch\Driver;
use Greenter\Model\Despatch\Shipment;
use Greenter\Model\Despatch\Transportist;
use Greenter\Model\Despatch\Vehicle;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\Note;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Report\HtmlReport;
use Greenter\Report\PdfReport;
use Greenter\Report\Resolver\DefaultTemplateResolver;
use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SunatService
{
    public function getSee($company)
    {
        $see = new See();
        $see->setCertificate(Storage::get($company->cert));
        $see->setService($company->production ? SunatEndpoints::FE_PRODUCCION : SunatEndpoints::FE_BETA);
        $see->setClaveSOL($company->ruc, $company->sol_user, $company->sol_pass);

        return $see;
    }

    public function getSeeApi($company)
    {
        $api = new Api($company->production ? [
            'auth' => 'https://api-seguridad.sunat.gob.pe/v1',
            'cpe' => 'https://api-cpe.sunat.gob.pe/v1'
        ] : [
            'auth' => 'https://gre-test.nubefact.com/v1',
            'cpe' => 'https://gre-test.nubefact.com/v1',
        ]);

        $api->setBuilderOptions([
            'strict_variables' => true,
            'optimizations' => 0,
            'debug' => true,
            'cache' => false,
        ])->setApiCredentials(
            $company->production ? $company->client_id : 'test-85e5b0ae-255c-4891-a595-0b98c65c9854',
            $company->production ? $company->client_secret : 'test-Hty/M6QshYvPgItX2P0+Kw=='
        )->setClaveSOL(
            $company->ruc,
            $company->production ? $company->sol_user : 'MODDATOS',
            $company->production ? $company->sol_pass : 'MODDATOS'
        )->setCertificate(Storage::get($company->cert_path));

        return $api;
    }

    public function getInvoice($data)
    {
        $invoice = (new Invoice())
            ->setUblVersion($data['ublVersion'] ?? '2.1') // UBL 2.1
            ->setTipoOperacion($data['tipoOperacion'] ?? null) // Venta - Catalog. 51
            ->setTipoDoc($data['tipoDoc'] ?? null) // Factura - Catalog. 01
            ->setSerie($data['serie'] ?? null)
            ->setCorrelativo($data['correlativo'] ?? null)
            ->setFechaEmision(new DateTime($data['fechaEmision'] ?? null)) // Zona horaria: Lima
            ->setFormaPago(new FormaPagoContado()) // FormaPago: Contado
            ->setTipoMoneda($data['tipoMoneda'] ?? null) // Sol - Catalog. 02
            ->setCompany($this->getCompany($data['company']))
            ->setClient($this->getClient($data['client']))
            ->setMtoOperGravadas($data['mtoOperGravadas'])
            ->setMtoOperExoneradas($data['mtoOperExoneradas'])
            ->setMtoOperInafectas($data['mtoOperInafectas'])
            ->setMtoOperExportacion($data['mtoOperExportacion'])
            ->setMtoOperGratuitas($data['mtoOperGratuitas'])
            ->setMtoIGV($data['mtoIGV'])
            ->setMtoIGVGratuitas($data['mtoIGVGratuitas'])
            ->setIcbper($data['icbper'])
            ->setTotalImpuestos($data['totalImpuestos'])
            ->setValorVenta($data['valorVenta'])
            ->setSubTotal($data['subTotal'])
            ->setRedondeo($data['redondeo'])
            ->setMtoImpVenta($data['mtoImpVenta'])
            ->setDetails($this->getDetails($data['details']))
            ->setLegends($this->getLegends($data['legends']));

        return $invoice;
    }

    public function getNote($data)
    {
        $note = (new Note())
            ->setUblVersion($data['ublVersion'] ?? '2.1') // UBL 2.1
            ->setTipoDoc($data['tipoDoc'] ?? null)
            ->setSerie($data['serie'] ?? null)
            ->setCorrelativo($data['correlativo'] ?? null)
            ->setFechaEmision(new DateTime($data['fechaEmision'] ?? null)) // Zona horaria: Lima
            ->setTipDocAfectado($data['tipDocAfectado'] ?? null) // Tipo de documento afectado - Catalog. 01
            ->setNumDocFectado($data['numDocFectado'] ?? null)
            ->setCodMotivo($data['codMotivo'] ?? null) // Codigo de motivo de la nota - Catalog. 09
            ->setDesMotivo($data['desMotivo'] ?? null) // Descripcion del motivo de la nota
            ->setTipoMoneda($data['tipoMoneda'] ?? null)
            ->setCompany($this->getCompany($data['company']))
            ->setClient($this->getClient($data['client']))
            ->setMtoOperGravadas($data['mtoOperGravadas'])
            ->setMtoOperExoneradas($data['mtoOperExoneradas'])
            ->setMtoOperInafectas($data['mtoOperInafectas'])
            ->setMtoOperExportacion($data['mtoOperExportacion'])
            ->setMtoIGV($data['mtoIGV'])
            ->setTotalImpuestos($data['totalImpuestos'])
            ->setValorVenta($data['valorVenta'])
            ->setSubTotal($data['subTotal'])
            ->setRedondeo($data['redondeo'])
            ->setMtoImpVenta($data['mtoImpVenta'])
            ->setDetails($this->getDetails($data['details']))
            ->setLegends($this->getLegends($data['legends']));

        return $note;
    }

    public function getDespatch($data)
    {
        $despatch = (new Despatch)
            ->setVersion($data['version'] ?? '2022')
            ->setTipoDoc($data['tipoDoc'] ?? '09') // Guia de remision - Catalog. 01
            ->setSerie($data['serie'] ?? null)
            ->setCorrelativo($data['correlativo'] ?? null)
            ->setFechaEmision(new DateTime($data['fechaEmision'] ?? null)) // Zona horaria: Lima
            ->setCompany($this->getCompany($data['company']))
            ->setDestinatario($this->getClient($data['destinatario']))
            ->setEnvio($this->getEnvio($data['envio']))
            ->setDetails($this->getDespatchDetails($data['details']));

        return $despatch;
    }

    public function getCompany($company)
    {
        $company = (new Company())
            ->setRuc($company['ruc'] ?? null)
            ->setRazonSocial($company['razonSocial'] ?? null)
            ->setNombreComercial($company['nombreComercial'] ?? null)
            ->setAddress($this->getAddress($company['address'] ?? null));

        return $company;
    }

    public function getClient($client)
    {
        $client = (new Client())
            ->setTipoDoc($client['tipoDoc'] ?? null) // RUC - Catalog. 06
            ->setNumDoc($client['numDoc'] ?? null)
            ->setRznSocial($client['rznSocial'] ?? null);

        return $client;
    }

    public function getAddress($address)
    {
        $address = (new Address())
            ->setUbigueo($address['ubigueo'] ?? null) // Codigo de ubigeo - Catalog. 01
            ->setDepartamento($address['departamento'] ?? null)
            ->setProvincia($address['provincia'] ?? null)
            ->setDistrito($address['distrito'] ?? null)
            ->setUrbanizacion($address['urbanizacion'] ?? null)
            ->setDireccion($address['direccion'] ?? null)
            ->setCodLocal($address['codLocal'] ?? null); // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.

        return $address;
    }

    public function getDetails($details)
    {
        $items = [];

        foreach ($details as $detail) {
            $items[] = (new SaleDetail())
                ->setCodProducto($detail['codProducto'] ?? null) // Codigo de producto del contribuyente
                ->setUnidad($detail['unidad'] ?? null) // Catalog. 03
                ->setDescripcion($detail['descripcion'] ?? null)
                ->setCantidad($detail['cantidad'] ?? null)
                ->setMtoValorUnitario($detail['mtoValorUnitario'] ?? null)
                ->setMtoValorVenta($detail['mtoValorVenta'] ?? null)
                ->setMtoBaseIgv($detail['mtoBaseIgv'] ?? null)
                ->setPorcentajeIgv($detail['porcentajeIgv'] ?? null) // 18%
                ->setIgv($detail['igv'] ?? null)
                ->setFactorIcbper($detail['factorIcbper'] ?? null)
                ->setIcbper($detail['icbper'] ?? null)
                ->setTipAfeIgv($detail['tipAfeIgv'] ?? null) // Gravado - Catalog. 07
                ->setTotalImpuestos($detail['totalImpuestos'] ?? null)
                ->setMtoPrecioUnitario($detail['mtoPrecioUnitario'] ?? null);
        }

        return $items;
    }

    public function getLegends($legends)
    {
        $items = [];

        foreach ($legends as $legend) {
            $items[] = (new Legend())
                ->setCode($legend['code'] ?? null) // Monto en letras - Catalog. 52
                ->setValue($legend['value'] ?? null);
        }

        return $items;
    }

    public function getEnvio($data)
    {
        $shipment = (new Shipment)
            ->setCodTraslado($data['codTraslado'] ?? null) // Catalog. 12
            ->setModTraslado($data['modTraslado'] ?? null) // Catalog. 13
            ->setFecTraslado(new DateTime($data['fecTraslado'] ?? null)) // Zona horaria: Lima
            ->setPesoTotal($data['pesoTotal'] ?? null)
            ->setUndPesoTotal($data['undPesoTotal'] ?? null) // Catalog. 14
            ->setLlegada(new Direction($data['llegada']['ubigueo'], $data['llegada']['direccion']))
            ->setPartida(new Direction($data['partida']['ubigueo'], $data['partida']['direccion']));

        if ($data['modTraslado'] == '01') {
            $shipment->setTransportista($this->getTransportista($data['transportista']));
        }

        if ($data['modTraslado'] == '02') {
            $shipment->setVehiculo($this->getVehiculo($data['vehiculos']))
                ->setChoferes($this->getChoferes($data['choferes']));
        }

        return $shipment;
    }

    public function getTransportista($data)
    {
        $transportista = (new Transportist)
            ->setTipoDoc($data['tipoDoc'] ?? null) // RUC - Catalog. 06
            ->setNumDoc($data['numDoc'] ?? null)
            ->setRznSocial($data['rznSocial'] ?? null)
            ->setNroMtc($data['nroMtc'] ?? null);

        return $transportista;
    }

    public function getVehiculo($vehiculos)
    {
        $vehiculos = collect($vehiculos);

        $secundarios = [];

        foreach ($vehiculos->slice(1) as $vehiculo) {
            $secundarios[] = (new Vehicle)
                ->setPlaca($vehiculo['placa'] ?? null);
        }

        $vehiculo = (new Vehicle)
            ->setPlaca($vehiculos->first()['placa'] ?? null)
            ->setSecundarios($secundarios);

        return $vehiculo;
    }

    public function getChoferes($choferes)
    {
        $choferes = collect($choferes);

        $drivers = [];

        $drivers[] = (new Driver)
            ->setTipo('Principal')
            ->setTipoDoc($choferes->first()['tipoDoc'] ?? null)
            ->setNroDoc($choferes->first()['nroDoc'] ?? null)
            ->setLicencia($choferes->first()['licencia'] ?? null)
            ->setNombres($choferes->first()['nombres'] ?? null)
            ->setApellidos($choferes->first()['apellidos'] ?? null);

        foreach ($choferes->slice(1) as $chofer) {
            $drivers[] = (new Driver)
                ->setTipo('Secundario')
                ->setTipoDoc($chofer['tipoDoc'] ?? null)
                ->setNroDoc($chofer['nroDoc'] ?? null)
                ->setLicencia($chofer['licencia'] ?? null)
                ->setNombres($chofer['nombres'] ?? null)
                ->setApellidos($chofer['apellidos'] ?? null);
        }

        return $drivers;
    }

    public function getDespatchDetails($details)
    {
        $items = [];

        foreach ($details as $detail) {
            $items[] = (new DespatchDetail)
                ->setCantidad($detail['cantidad'] ?? null)
                ->setUnidad($detail['unidad'] ?? null)
                ->setDescripcion($detail['descripcion'] ?? null)
                ->setCodigo($detail['codigo'] ?? null);
        }

        return $items;
    }

    public function sunatResponse($result)
    {
        $response['success'] = $result->isSuccess();

        // Verificamos que la conexión con SUNAT fue exitosa.

        if (!$response['success']) {
            // Mostrar error al conectarse a SUNAT.

            $response['error'] = [
                'code' => $result->getError()->getCode(),
                'message' => $result->getError()->getMessage(),
            ];

            return $response;
        }

        $cdr = $result->getCdrResponse();

        $response['cdrResponse'] = [
            'code' => (int)$cdr->getCode(),
            'description' => $cdr->getDescription(),
            'notes' => $cdr->getNotes(),
        ];

        return $response;
    }

    public function saveInvoiceXml($company, $invoice, $see)
    {
        if ($see->getFactory()->getLastXml()) {
            if (!Storage::exists('invoices/' . $company->ruc . '/' . $invoice->getName() . '/' . $invoice->getName() . '.xml')) {
                $path_xml = 'invoices/' . $company->ruc . '/' . $invoice->getName() . '/' . $invoice->getName() . '.xml';

                Storage::put($path_xml, $see->getFactory()->getLastXml());

                return 'invoices/' . $company->ruc . '/' . $invoice->getName() . '/' . $invoice->getName() . '.xml';
            }
        }
    }

    public function saveInvoiceCdr($company, $invoice, $result)
    {
        if ($result->getCdrZip()) {
            if (!Storage::exists('invoices/' . $company->ruc . '/' . $invoice->getName() . '/' . $invoice->getName() . '.zip')) {
                $path_cdr = 'invoices/' . $company->ruc . '/' . $invoice->getName() . '/' . $invoice->getName() . '.zip';

                Storage::put($path_cdr, $result->getCdrZip());

                return 'invoices/' . $company->ruc . '/' . $invoice->getName() . '/' . $invoice->getName() . '.zip';
            }
        }
    }

    public function saveNoteXml($company, $note, $see)
    {
        if ($see->getFactory()->getLastXml()) {
            if (!Storage::exists('notes/' . $company->ruc . '/' . $note->getName() . '/' . $note->getName() . '.xml')) {
                $path_xml = 'notes/' . $company->ruc . '/' . $note->getName() . '/' . $note->getName() . '.xml';

                Storage::put($path_xml, $see->getFactory()->getLastXml());

                return 'notes/' . $company->ruc . '/' . $note->getName() . '/' . $note->getName() . '.xml';
            }
        }
    }

    public function saveNoteCdr($company, $note, $result)
    {
        if ($result->getCdrZip()) {
            if (!Storage::exists('notes/' . $company->ruc . '/' . $note->getName() . '/' . $note->getName() . '.zip')) {
                $path_cdr = 'notes/' . $company->ruc . '/' . $note->getName() . '/' . $note->getName() . '.zip';

                Storage::put($path_cdr, $result->getCdrZip());

                return 'notes/' . $company->ruc . '/' . $note->getName() . '/' . $note->getName() . '.zip';
            }
        }
    }

    public function saveDespatchXml($company, $despatch, $api)
    {
        if ($api->getLastXml()) {
            if (!Storage::exists('despatches/' . $company->ruc . '/' . $despatch->getName() . '/' . $despatch->getName() . '.xml')) {
                $path_xml = 'despatches/' . $company->ruc . '/' . $despatch->getName() . '/' . $despatch->getName() . '.xml';

                Storage::put($path_xml, $api->getLastXml());

                return 'despatches/' . $company->ruc . '/' . $despatch->getName() . '/' . $despatch->getName() . '.xml';
            }
        }
    }

    public function saveDespatchCdr($company, $despatch, $result)
    {
        if ($result->getCdrZip()) {
            if (!Storage::exists('despatches/' . $company->ruc . '/' . $despatch->getName() . '/' . $despatch->getName() . '.zip')) {
                $path_cdr = 'despatches/' . $company->ruc . '/' . $despatch->getName() . '/' . $despatch->getName() . '.zip';

                Storage::put($path_cdr, $result->getCdrZip());

                return 'despatches/' . $company->ruc . '/' . $despatch->getName() . '/' . $despatch->getName() . '.zip';
            }
        }
    }

    public function getHtmlReport($file, $hash)
    {
        $report = new HtmlReport();

        $resolver = new DefaultTemplateResolver();
        $report->setTemplate($resolver->getTemplate($file));

        $ruc = $file->getCompany()->getRuc();
        $company = ModelsCompany::where('ruc', $ruc)->first();

        $params = [
            'system' => [
                'logo' => Storage::get($company->image->path),
                'hash' => $hash,
            ],
            'user' => [
                'header'     => '',
                'extras'     => [
                    ['name' => 'CONDICION DE PAGO', 'value' => 'Efectivo'],
                    ['name' => 'VENDEDOR', 'value' => $company->razon_social],
                ],
                'footer' => '<p></p>'
            ]
        ];

        return $report->render($file, $params);
    }

    public function generatePdfReport($file, $hash)
    {
        $htmlReport = new HtmlReport();
        $resolver = new DefaultTemplateResolver();
        $htmlReport->setTemplate($resolver->getTemplate($file));

        $report = new PdfReport($htmlReport);

        $report->setOptions([
            'no-outline',
            'viewport-size' => '1280x1024',
            'page-width' => '21cm',
            'page-height' => '29.7cm',
        ]);

        $report->setBinPath(env('WKHTMLTOPDF'));

        $ruc = $file->getCompany()->getRuc();
        $company = ModelsCompany::where('ruc', $ruc)->where('user_id', Auth::id())->first();

        $params = [
            'system' => [
                'logo' => Storage::get($company->image),
                'hash' => $hash,
            ],
            'user' => [
                'header'     => '',
                'extras'     => [
                    ['name' => 'CONDICION DE PAGO', 'value' => 'Efectivo'],
                    ['name' => 'VENDEDOR', 'value' => $company->razon_social],
                ],
                'footer' => '<p></p>'
            ]
        ];

        $pdf = $report->render($file, $params);

        if ($pdf === null) {
            $error = $report->getExporter()->getError();
            echo 'Error: ' . $error;
            return;
        }

        if (!Storage::exists('pdf/' . $company->ruc . '/' . $file->getName() . '/' . $file->getName() . '.pdf')) {
            Storage::put('pdf/' . $company->ruc . '/' . $file->getName() . '/' . $file->getName() . '.pdf', $pdf);

            return 'pdf/' . $company->ruc . '/' . $file->getName() . '/' . $file->getName() . '.pdf';
        }
    }
}
