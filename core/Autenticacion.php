<?php
/**
 * Clase para realizar autenticación automática ante el SII y obtener el token
 * necesario para las transacciones con el sitio.
 */
class Autenticacion
{
    private static function getSeed()
    {
        $xml = Sii::request('CrSeed', 'getSeed');
        if ($xml===false or (string)$xml->xpath('/SII:RESPUESTA/SII:RESP_HDR/ESTADO')[0]!=='00') {
            Log::write(
                Estado::AUTH_ERROR_SEMILLA,
                Estado::get(Estado::AUTH_ERROR_SEMILLA)
            );
            return false;
        }
        return (string)$xml->xpath('/SII:RESPUESTA/SII:RESP_BODY/SEMILLA')[0];
    }
    
    private static function getTokenRequest($seed, $Firma = [])
    {
        if (is_array($Firma))
            $Firma = new FirmaElectronica($Firma);
        $seedSigned = $Firma->signXML(
            (new XML())->generate([
                'getToken' => [
                    'item' => [
                        'Semilla' => $seed
                    ]
                ]
            ])->saveXML()
        );
        if (!$seedSigned) {
            Log::write(
                Estado::AUTH_ERROR_FIRMA_SOLICITUD_TOKEN,
                Estado::get(Estado::AUTH_ERROR_FIRMA_SOLICITUD_TOKEN)
            );
            return false;
        }
        return $seedSigned;
    }

    public static function getToken($Firma = [])
    {
        if (!$Firma) return false;
        $semilla = self::getSeed();
        if (!$semilla) return false;
        $requestFirmado = self::getTokenRequest($semilla, $Firma);
        if (!$requestFirmado) return false;
        $xml = Sii::request('GetTokenFromSeed', 'getToken', $requestFirmado);
        if ($xml===false or (string)$xml->xpath('/SII:RESPUESTA/SII:RESP_HDR/ESTADO')[0]!=='00') {
            Log::write(
                Estado::AUTH_ERROR_TOKEN,
                Estado::get(Estado::AUTH_ERROR_TOKEN)
            );
            return false;
        }
        return (string)$xml->xpath('/SII:RESPUESTA/SII:RESP_BODY/TOKEN')[0];
    }

}
