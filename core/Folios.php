<?php
/**
 * Clase para realizar operaciones con lo Folios autorizados por el SII
 */
class Folios
{
    private $xml; ///< Objeto XML que representa el CAF

    public function __construct($xml)
    {
        $this->xml = new XML();
        $this->xml->loadXML(utf8_encode($xml));
    }

    public function check()
    {
        // validar firma del SII sobre los folios
        $firma = $this->getFirma();
        $idk = $this->getIDK();
        if (!$firma or !$idk)
            return false;
        $pub_key = Sii::cert($idk);
        if (!$pub_key or openssl_verify($this->xml->getFlattened('/AUTORIZACION/CAF/DA'), base64_decode($firma), $pub_key)!==1) {
            Log::write(
                Estado::FOLIOS_ERROR_FIRMA,
                Estado::get(Estado::FOLIOS_ERROR_FIRMA)
            );
            return false;
        }
        // validar clave privada y pública proporcionada por el SII
        $private_key = $this->getPrivateKey();
        if (!$private_key)
            return false;
        $plain = md5(date('U'));
        if (!openssl_private_encrypt($plain, $crypt, $private_key)) {
            Log::write(
                Estado::FOLIOS_ERROR_ENCRIPTAR,
                Estado::get(Estado::FOLIOS_ERROR_ENCRIPTAR)
            );
            return false;
        }
        $public_key = $this->getPublicKey();
        if (!$public_key)
            return false;
        if (!openssl_public_decrypt($crypt, $plain_firmado, $public_key)) {
            Log::write(
                Estado::FOLIOS_ERROR_DESENCRIPTAR,
                Estado::get(Estado::FOLIOS_ERROR_DESENCRIPTAR)
            );
            return false;
        }
        return $plain === $plain_firmado;
    }

    public function getCaf()
    {
        if (!$this->xml)
            return false;
        $CAF = $this->xml->getElementsByTagName('CAF')->item(0);
        return $CAF ? $CAF : false;
    }

    public function getEmisor()
    {
        if (!$this->xml)
            return false;
        $RE = $this->xml->getElementsByTagName('RE')->item(0);
        return $RE ? $RE->nodeValue : false;
    }

    public function getDesde()
    {
        if (!$this->xml)
            return false;
        $D = $this->xml->getElementsByTagName('D')->item(0);
        return $D ? (int)$D->nodeValue : false;
    }

    public function getHasta()
    {
        if (!$this->xml)
            return false;
        $H = $this->xml->getElementsByTagName('H')->item(0);
        return $H ? (int)$H->nodeValue : false;
    }

    public function getFecha()
    {
        if (!$this->xml)
            return false;
        $FA = $this->xml->getElementsByTagName('FA')->item(0);
        return $FA ? $FA->nodeValue : false;
    }

    private function getFirma()
    {
        if (!$this->xml)
            return false;
        $FRMA = $this->xml->getElementsByTagName('FRMA')->item(0);
        return $FRMA ? $FRMA->nodeValue : false;
    }

    private function getIDK()
    {
        if (!$this->xml)
            return false;
        $IDK = $this->xml->getElementsByTagName('IDK')->item(0);
        return $IDK ? (int)$IDK->nodeValue : false;
    }

    public function getPrivateKey()
    {
        if (!$this->xml)
            return false;
        $RSASK = $this->xml->getElementsByTagName('RSASK')->item(0);
        return $RSASK ? $RSASK->nodeValue : false;
    }

    public function getPublicKey()
    {
        if (!$this->xml)
            return false;
        $RSAPUBK = $this->xml->getElementsByTagName('RSAPUBK')->item(0);
        return $RSAPUBK ? $RSAPUBK->nodeValue : false;
    }

    public function getTipo()
    {
        if (!$this->xml)
            return false;
        $TD = $this->xml->getElementsByTagName('TD')->item(0);
        return $TD ? (int)$TD->nodeValue : false;
    }

    public function getCertificacion()
    {
        $idk = $this->getIDK();
        return $idk ?  $idk === 100 : null;
    }

    public function saveXML()
    {
        return $this->xml->saveXML();
    }

}
