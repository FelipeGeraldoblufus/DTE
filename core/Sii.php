<?php
/**
 * Clase para acciones genéricas asociadas al SII de Chile
 */
class Sii
{
    private static $config = [
        'wsdl' => [
            '*' => 'https://{servidor}.sii.cl/DTEWS/{servicio}.jws?WSDL',
            'QueryEstDteAv' => 'https://{servidor}.sii.cl/DTEWS/services/{servicio}?WSDL',
            'QueryEstUp' => 'https://{servidor}.sii.cl/DTEWS/services/{servicio}?WSDL',
            'wsDTECorreo' => 'https://{servidor}.sii.cl/DTEWS/services/{servicio}?WSDL',
        ],
        'servidor' => ['palena', 'maullin'], ///< servidores 0: producción, 1: certificación
        'certs' => [300, 100], ///< certificados 0: producción, 1: certificación
    ];

    const PRODUCCION = 0; ///< Constante para indicar ambiente de producción
    const CERTIFICACION = 1; ///< Constante para indicar ambiente de desarrollo

    const IVA = 19; ///< Tasa de IVA

    private static $retry = 10; ///< Veces que se reintentará conectar a SII al usar el servicio web
    private static $verificar_ssl = true; ///< Indica si se deberá verificar o no el certificado SSL del SII
    private static $ambiente = self::CERTIFICACION; ///< Ambiente que se utilizará

    private static $direcciones_regionales = [
        'CHILLÁN VIEJO' => 'CHILLÁN',
        'HUECHURABA' => 'SANTIAGO NORTE',
        'LA CISTERNA' => 'SANTIAGO SUR',
        'LAS CONDES' => 'SANTIAGO ORIENTE',
        'LO ESPEJO' => 'SANTIAGO SUR',
        'PEÑALOLÉN' => 'ÑUÑOA',
        'PUDAHUEL' => 'SANTIAGO PONIENTE',
        'RECOLETA' => 'SANTIAGO NORTE',
        'SANTIAGO' => 'SANTIAGO CENTRO',
        'SAN MIGUEL' => 'SANTIAGO SUR',
        'SAN VICENTE' => 'SAN VICENTE TAGUA TAGUA',
        'TALTAL' => 'ANTOFAGASTA',
        'VITACURA' => 'SANTIAGO ORIENTE',
    ]; /// Direcciones regionales del SII según la comuna

    public static function setServidor($servidor = 'palena', $certificacion = Sii::CERTIFICACION)
    {
        self::$config['servidor'][$certificacion] = $servidor;
    }

    public static function getServidor($ambiente = null)
    {
        return self::$config['servidor'][self::getAmbiente($ambiente)];
    }

    public static function wsdl($servicio, $ambiente = null)
    {
        // determinar ambiente que se debe usar
        $ambiente = self::getAmbiente($ambiente);
        // entregar WSDL local (modificados para ambiente de certificación)
        if ($ambiente==self::CERTIFICACION) {
            $wsdl = dirname(dirname(__FILE__)).'/wsdl/'.self::$config['servidor'][$ambiente].'/'.$servicio.'.jws';
            if (is_readable($wsdl))
                return $wsdl;
        }
        // entregar WSDL oficial desde SII
        $location = isset(self::$config['wsdl'][$servicio]) ? self::$config['wsdl'][$servicio] : self::$config['wsdl']['*'];
        $wsdl = str_replace(
            ['{servidor}', '{servicio}'],
            [self::$config['servidor'][$ambiente], $servicio],
            $location
        );
        // entregar wsdl
        return $wsdl;
    }

    public static function request($wsdl, $request, $args = null, $retry = null)
    {
        if (is_numeric($args)) {
            $retry = (int)$args;
            $args = null;
        }
        if (!$retry)
            $retry = self::$retry;
        if ($args and !is_array($args))
            $args = [$args];
        if (!self::$verificar_ssl) {
            if (self::getAmbiente()==self::PRODUCCION) {
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
            $soap = new \SoapClient(self::wsdl($wsdl), $options);
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
                if ($args) {
                    $body = call_user_func_array([$soap, $request], $args);
                } else {
                    $body = $soap->$request();
                }
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
        return new \SimpleXMLElement($body, LIBXML_COMPACT);
    }

    public static function setVerificarSSL($verificar = true)
    {
        self::$verificar_ssl = $verificar;
    }

    public static function getVerificarSSL()
    {
        return self::$verificar_ssl;
    }

    public static function enviar($usuario, $empresa, $dte, $token, $retry = null)
    {
        // definir datos que se usarán en el envío
        list($rutSender, $dvSender) = explode('-', str_replace('.', '', $usuario));
        list($rutCompany, $dvCompany) = explode('-', str_replace('.', '', $empresa));
        if (strpos($dte, '<?xml')===false) {
            $dte = '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n".$dte;
        }
        do {
            $file = sys_get_temp_dir().'/dte_'.md5(microtime().$token.$dte).'.xml';
        } while (file_exists($file));
        file_put_contents($file, $dte);
        $data = [
            'rutSender' => $rutSender,
            'dvSender' => $dvSender,
            'rutCompany' => $rutCompany,
            'dvCompany' => $dvCompany,
            'archivo' => curl_file_create(
                $file,
                'application/xml',
                basename($file)
            ),
        ];
        // definir reintentos si no se pasaron
        if (!$retry)
            $retry = self::$retry;
        // crear sesión curl con sus opciones
        $curl = curl_init();
        $header = [
            'User-Agent: Mozilla/4.0 (compatible; PROG 1.0;)',
            'Referer: ',
            'Cookie: TOKEN='.$token,
        ];
        $url = 'https://'.self::$config['servidor'][self::getAmbiente()].'.sii.cl/cgi_dte/UPL/DTEUpload';
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // si no se debe verificar el SSL se asigna opción a curl, además si
        // se está en el ambiente de producción y no se verifica SSL se
        // generará una entrada en el log
        if (!self::$verificar_ssl) {
            if (self::getAmbiente()==self::PRODUCCION) {
                $msg = Estado::get(Estado::ENVIO_SSL_SIN_VERIFICAR);
                Log::write(Estado::ENVIO_SSL_SIN_VERIFICAR, $msg, LOG_WARNING);
            }
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }
        // enviar XML al SII
        for ($i=0; $i<$retry; $i++) {
            $response = curl_exec($curl);
            if ($response and $response!='Error 500')
                break;
        }
        unlink($file);
        // verificar respuesta del envío y entregar error en caso que haya uno
        if (!$response or $response=='Error 500') {
            if (!$response)
                Log::write(Estado::ENVIO_ERROR_CURL, Estado::get(Estado::ENVIO_ERROR_CURL, curl_error($curl)));
            if ($response=='Error 500')
                Log::write(Estado::ENVIO_ERROR_500, Estado::get(Estado::ENVIO_ERROR_500));
            return false;
        }
        // cerrar sesión curl
        curl_close($curl);
        // crear XML con la respuesta y retornar
        try {
            $xml = new \SimpleXMLElement($response, LIBXML_COMPACT);
        } catch (Exception $e) {
            Log::write(Estado::ENVIO_ERROR_XML, Estado::get(Estado::ENVIO_ERROR_XML, $e->getMessage()));
            return false;
        }
        if ($xml->STATUS!=0) {
            Log::write(
                $xml->STATUS,
                Estado::get($xml->STATUS).(isset($xml->DETAIL)?'. '.implode("\n", (array)$xml->DETAIL->ERROR):'')
            );
        }
        return $xml;
    }

    public static function cert($idk = null)
    {
        // si se pasó un idk y existe el archivo asociado se entrega
        if ($idk) {
            $cert = dirname(dirname(__FILE__)).'/certs/'.$idk.'.cer';
            if (is_readable($cert))
                return file_get_contents($cert);
        }
        // buscar certificado y entregar si existe o =false si no
        $ambiente = self::getAmbiente();
        $cert = dirname(dirname(__FILE__)).'/certs/'.self::$config['certs'][$ambiente].'.cer';
        if (!is_readable($cert)) {
            Log::write(Estado::SII_ERROR_CERTIFICADO, Estado::get(Estado::SII_ERROR_CERTIFICADO, self::$config['certs'][$ambiente]));
            return false;
        }
        return file_get_contents($cert);
    }

    public static function setAmbiente($ambiente = self::PRODUCCION)
    {
        $ambiente = $ambiente ? self::CERTIFICACION : self::PRODUCCION;
        if ($ambiente==self::CERTIFICACION) {
            self::setVerificarSSL(false);
        }
        self::$ambiente = $ambiente;
    }

    public static function getAmbiente($ambiente = null)
    {
        if ($ambiente===null) {
            if (defined('_CERTIFICACION_'))
                $ambiente = (int)_CERTIFICACION_;
            else
                $ambiente = self::$ambiente;
        }
        return $ambiente;
    }

    public static function getIVA()
    {
        return self::IVA;
    }

    public static function getContribuyentes(FirmaElectronica $Firma, $ambiente = null, $dia = null)
    {
        // solicitar token
        $token = Autenticacion::getToken($Firma);
        if (!$token)
            return false;
        // definir ambiente y servidor
        $ambiente = self::getAmbiente($ambiente);
        $servidor = self::$config['servidor'][$ambiente];
        // preparar consulta curl
        $curl = curl_init();
        $header = [
            'User-Agent: Mozilla/4.0 (compatible; PROG 1.0;)',
            'Referer: https://'.$servidor.'.sii.cl/cvc/dte/ee_empresas_dte.html',
            'Cookie: TOKEN='.$token,
            'Accept-Encoding' => 'gzip, deflate, sdch',
        ];
        $dia = $dia===null ? date('Ymd') : str_replace('-', '', $dia);
        $url = 'https://'.$servidor.'.sii.cl/cvc_cgi/dte/ee_consulta_empresas_dwnld?NOMBRE_ARCHIVO=ce_empresas_dwnld_'.$dia.'.csv';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // si no se debe verificar el SSL se asigna opción a curl, además si
        // se está en el ambiente de producción y no se verifica SSL se
        // generará un error de nivel E_USER_NOTICE
        if (!self::$verificar_ssl) {
            if ($ambiente==self::PRODUCCION) {
                $msg = Estado::get(Estado::ENVIO_SSL_SIN_VERIFICAR);
                Log::write(Estado::ENVIO_SSL_SIN_VERIFICAR, $msg, LOG_WARNING);
            }
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }
        // realizar consulta curl
        $response = curl_exec($curl);
        if (!$response)
            return false;
        // cerrar sesión curl
        curl_close($curl);
        // entregar datos del archivo CSV
        ini_set('memory_limit', '1024M');
        $lines = explode("\n", $response);
        $n_lines = count($lines);
        $data = [];
        for ($i=1; $i<$n_lines; $i++) {
            $row = str_getcsv($lines[$i], ';', '');
            unset($lines[$i]);
            if (!isset($row[5]))
                continue;
            for ($j=0; $j<6; $j++)
                $row[$j] = trim($row[$j]);
            $row[1] = utf8_decode($row[1]);
            $row[4] = strtolower($row[4]);
            $row[5] = strtolower($row[5]);
            $data[] = $row;
        }
        return $data;
    }

    public static function getDireccionRegional($comuna)
    {
        if (!is_numeric($comuna)) {
            $direccion = mb_strtoupper($comuna, 'UTF-8');
            return isset(self::$direcciones_regionales[$direccion]) ? self::$direcciones_regionales[$direccion] : $direccion;
        }
        return 'SUC '.$comuna;
    }


    /*public static function consultarEstadoEnvio($rutEmisor, $trackId, $token) {
        list($rutCompania, $dvCompania) = explode('-', str_replace('.', '', $rutEmisor));
        
        try {
            $client = new \SoapClient('https://maullin.sii.cl/DTEWS/QueryEstUp.jws?WSDL');
            
            // Agregar token al header
            $headers = [
                new \SoapHeader(
                    'http://sii.cl/XMLSchema', 
                    'token', 
                    $token
                )
            ];
            $client->__setSoapHeaders($headers);
            
            $response = $client->getEstUp([
                'TOKEN' => $token,        // También en el body
                'rutCompania' => $rutCompania,
                'dvCompania' => $dvCompania,
                'trackId' => $trackId
            ]);
            
            var_dump('Respuesta SOAP:', $response);
            
            return $response;
            
        } catch (\Exception $e) {
            var_dump('Error SOAP:', $e->getMessage());
            return false;
        }
    }*/

    public static function consultarEstadoEnvio($rutEmisor, $trackId, $token) {
        if (!$token) {
            Log::write('DEBUG_QUERYESTUP', 'Error: Token no proporcionado');
            return false;
        }
    
        list($rutCompania, $dvCompania) = explode('-', str_replace('.', '', $rutEmisor));
        
        try {
            $client = new \SoapClient('https://maullin.sii.cl/DTEWS/QueryEstUp.jws?WSDL', [
                'trace' => true,
                'exceptions' => true
            ]);
    
            $params = (object)[
                'RutCompania' => $rutCompania,
                'DvCompania' => $dvCompania,
                'TrackId' => $trackId,
                'Token' => $token
            ];
    
            Log::write('DEBUG_QUERYESTUP', 'Parámetros de consulta:');
            Log::write('DEBUG_QUERYESTUP', print_r($params, true));
    
            $response = $client->getEstUp($params);
            
            Log::write('DEBUG_QUERYESTUP', 'Respuesta:');
            Log::write('DEBUG_QUERYESTUP', print_r($response, true));
    
            return $response;
            
        } catch (\Exception $e) {
            Log::write('DEBUG_QUERYESTUP', 'Error: ' . $e->getMessage());
            return false;
        }
    }
}
