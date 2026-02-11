<?php
/**
 * Clase con las acciones asociadas al registro de compras y ventas del SII
 */
class RegistroCompraVenta
{
    public static $dtes = [
        33 => 'Factura electrónica',
        34 => 'Factura no afecta o exenta electrónica',
        43 => 'Liquidación factura electrónica',
    ]; ///< Documentos que tienen acuse de recibo

    public static $acciones = [
        'ERM' => 'Otorga recibo de mercaderías o servicios',
        'ACD' => 'Acepta contenido del documento',
        'RCD' => 'Reclamo al contenido del documento',
        'RFP' => 'Reclamo por falta parcial de mercaderías',
        'RFT' => 'Reclamo por falta total de mercaderías',
    ]; ///< Posibles acciones a que tiene asociadas un DTE

    public static $eventos = [
        'A' => 'No reclamado en plazo (recepción automática)',
        'C' => 'Recibo otorgado por el receptor',
        'P' => 'Forma de pago al contado',
        'R' => 'Reclamado',
    ];

    public static $tipo_transacciones = [
        1 => 'Compras del giro',
        2 => 'Compras en supermercados o comercios similares',
        3 => 'Adquisición de bienes raíces',
        4 => 'Compra de activo fijo',
        5 => 'Compras con IVA uso común',
        6 => 'Compras sin derecho a crédito',
    ]; ///< Tipos de transacciones o caracterizaciones/clasificaciones de las compras

    private static $wsdl = [
        'https://ws1.sii.cl/WSREGISTRORECLAMODTE/registroreclamodteservice?wsdl',
        'https://ws2.sii.cl/WSREGISTRORECLAMODTECERT/registroreclamodteservice?wsdl',
    ]; ///< Rutas de los WSDL de producción y certificación

    private $token; ///< Token que se usará en la sesión de consultas al RCV

    /**
     * Constructor, obtiene el token de la sesión y lo guarda
     */
    public function __construct(FirmaElectronica $Firma)
    {
        $this->token = Autenticacion::getToken($Firma);
        if (!$this->token) {
            throw new \Exception('No fue posible obtener el token para la sesión del RCV');
        }
    }

    /**
     * Método que ingresa una acción al registro de compr/venta en el SII
     */
    public function ingresarAceptacionReclamoDoc($rut, $dv, $dte, $folio, $accion)
    {
        // ingresar acción al DTE
        $r = $this->request('ingresarAceptacionReclamoDoc', [
            'rutEmisor' => $rut,
            'dvEmisor' => $dv,
            'tipoDoc' => $dte,
            'folio' => $folio,
            'accionDoc' => $accion,
        ]);
        // si no se pudo recuperar error
        if ($r===false) {
            return false;
        }
        // entregar resultado del ingreso
        return [
            'codigo' => $r->codResp,
            'glosa' => $r->descResp,
        ];
    }

    /**
     * Método que entrega los eventos asociados a un DTE
     */
    public function listarEventosHistDoc($rut, $dv, $dte, $folio)
    {
        // consultar eventos del DTE
        $r = $this->request('listarEventosHistDoc', [
            'rutEmisor' => $rut,
            'dvEmisor' => $dv,
            'tipoDoc' => $dte,
            'folio' => $folio,
        ]);
        // si no se pudo recuperar error
        if ($r===false) {
            return false;
        }
        // si hubo error informar
        if (!in_array($r->codResp, [8, 15, 16])) {
            throw new \Exception($r->descResp);
        }
        // entregar eventos del DTE
        $eventos = [];
        if (!empty($r->listaEventosDoc)) {
            if (!is_array($r->listaEventosDoc)) {
                $r->listaEventosDoc = [$r->listaEventosDoc];
            }
            foreach ($r->listaEventosDoc as $evento) {
                $eventos[] = [
                    'codigo' => $evento->codEvento,
                    'glosa' => $evento->descEvento,
                    'responsable' => $evento->rutResponsable.'-'.$evento->dvResponsable,
                    'fecha' => $evento->fechaEvento,
                ];
            }
        }
        return $eventos;
    }

    /**
     * Entrega información de cesión para el DTE, si es posible o no cederlo
     */
    public function consultarDocDteCedible($rut, $dv, $dte, $folio)
    {
        // consultar eventos del DTE
        $r = $this->request('consultarDocDteCedible', [
            'rutEmisor' => $rut,
            'dvEmisor' => $dv,
            'tipoDoc' => $dte,
            'folio' => $folio,
        ]);
        // si no se pudo recuperar error
        if ($r===false) {
            return false;
        }
        // entregar información de cesión para el DTE
        return [
            'codigo' => $r->codResp,
            'glosa' => $r->descResp,
        ];
    }

    public function consultarFechaRecepcionSii($rut, $dv, $dte, $folio)
    {
        // consultar eventos del DTE
        $r = $this->request('consultarFechaRecepcionSii', [
            'rutEmisor' => $rut,
            'dvEmisor' => $dv,
            'tipoDoc' => $dte,
            'folio' => $folio,
        ]);
        // si no se pudo recuperar error
        if (!$r) {
            return false;
        }
        // armar y entregar fecha
        list($dia, $hora) = explode(' ', $r);
        list($d, $m, $Y) = explode('-', $dia);
        return $Y.'-'.$m.'-'.$d.' '.$hora;
    }

    /**
     * Método para realizar una solicitud al servicio web del SII
     */
    private function request($request, $args, $retry = 10)
    {
        if (!Sii::getVerificarSSL()) {
            if (Sii::getAmbiente()==Sii::PRODUCCION) {
                $msg = Estado::get(Estado::ENVIO_SSL_SIN_VERIFICAR);
                Log::write(Estado::ENVIO_SSL_SIN_VERIFICAR, $msg, LOG_WARNING);
            }
            $options = ['stream_context' => stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ])];
        } else {
            $options = [];
        }
        try {
            $wsdl = self::$wsdl[Sii::getAmbiente()];
            $soap = new \SoapClient($wsdl, $options);
            $soap->__setCookie('TOKEN', $this->token);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (isset($e->getTrace()[0]['args'][1]) and is_string($e->getTrace()[0]['args'][1])) {
                $msg .= ': '.$e->getTrace()[0]['args'][1];
            }
            Log::write(Estado::REQUEST_ERROR_SOAP, Estado::get(Estado::REQUEST_ERROR_SOAP, $msg));
            return false;
        }
        for ($i=0; $i<$retry; $i++) {
            try {
                $body = call_user_func_array([$soap, $request], $args);
                break;
            } catch (\Exception $e) {
                $msg = $e->getMessage();
                if (isset($e->getTrace()[0]['args'][1]) and is_string($e->getTrace()[0]['args'][1])) {
                    $msg .= ': '.$e->getTrace()[0]['args'][1];
                }
                Log::write(Estado::REQUEST_ERROR_SOAP, Estado::get(Estado::REQUEST_ERROR_SOAP, $msg));
                $body = null;
            }
        }
        if ($body===null) {
            Log::write(Estado::REQUEST_ERROR_BODY, Estado::get(Estado::REQUEST_ERROR_BODY, $wsdl, $retry));
            return false;
        }
        return $body;
    }

}
