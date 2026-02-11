<?php
/**
 * Clase para el manejo de Boletas Electrónicas
 * Extiende la funcionalidad de EnvioDte para manejar boletas específicamente
 */
class EnvioBoleta extends EnvioDte {
    
    private $tipo = 1; // Indica que es una boleta

    public function setCaratula(array $caratula) 
    {
        // Validar que hayan DTEs
        if (!isset($this->dtes[0])) {
            Log::write(Estado::ENVIODTE_FALTA_DTE, Estado::get(Estado::ENVIODTE_FALTA_DTE));
            return false;
        }

        // Generar caratula específica para boletas
        $this->caratula = array_merge([
            '@attributes' => [
                'version' => '1.0'
            ],
            'RutEmisor' => $this->dtes[0]->getEmisor(),
            'RutEnvia' => isset($this->Firma) ? $this->Firma->getID() : false,
            'RutReceptor' => '60803000-K', // SII
            'FchResol' => $caratula['FchResol'],
            'NroResol' => $caratula['NroResol'],
            'TmstFirmaEnv' => date('Y-m-d\TH:i:s'),
            'SubTotDTE' => $this->getSubTotDTE()
        ], $caratula);

        $this->id = 'EnvioBOLETA_'.str_replace('-', '', $this->caratula['RutEmisor']).'_'.date('U');
        return true;
    }

    public function generar()
    {
        // Si ya se había generado se entrega directamente
        if ($this->xml_data)
            return $this->xml_data;

        // Generar XML del envío
        $xmlEnvio = (new XML())->generate([
            'EnvioBOLETA' => [
                '@attributes' => [
                    'xmlns' => 'http://www.sii.cl/SiiDte',
                    'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                    'xsi:schemaLocation' => 'http://www.sii.cl/SiiDte EnvioBOLETA_v11.xsd',
                    'version' => '1.0'
                ],
                'SetDTE' => [
                    '@attributes' => [
                        'ID' => $this->id
                    ],
                    'Caratula' => $this->caratula,
                    'DTE' => null,
                ]
            ]
        ])->saveXML();

        // Generar XML de los DTE que se deberán incorporar
        $DTEs = [];
        foreach ($this->dtes as &$DTE) {
            $DTEs[] = trim(str_replace(['<?xml version="1.0" encoding="ISO-8859-1"?>', '<?xml version="1.0"?>'], '', $DTE->saveXML()));
        }

        // Firmar XML del envío y entregar
        $xml = str_replace('<DTE/>', implode("\n", $DTEs), $xmlEnvio);
        $this->xml_data = $this->Firma ? $this->Firma->signXML($xml, '#'.$this->id, 'SetDTE', true) : $xml;
        return $this->xml_data;
    }

    public function enviar()
    {
        // Generar XML 
        if (!$this->xml_data)
            $this->xml_data = $this->generar();
        if (!$this->xml_data)
            return false;

        // Validar schema antes de enviar
        if (!$this->schemaValidate())
            return false;

        // Solicitar token
        $token = Autenticacion::getToken($this->Firma);
        if (!$token)
            return false;

        // Enviar al SII
        $result = Sii::enviar(
            $this->caratula['RutEnvia'],
            $this->caratula['RutEmisor'],
            $this->xml_data,
            $token
        );

        if ($result===false)
            return false;
        if (!is_numeric((string)$result->TRACKID))
            return false;
        return (int)(string)$result->TRACKID;
    }

}