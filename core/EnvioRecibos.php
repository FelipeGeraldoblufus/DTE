<?php
/**
 * Clase que representa el envío de un recibo por entrega de mercadería o
 * servicios prestados por un proveedor
 */
class EnvioRecibos extends Documento
{

    private $recibos = []; ///< recibos que se adjuntarán

    public function agregar(array $datos)
    {
        $this->recibos[] = [
            '@attributes' => [
                'version' => '1.0',
            ],
            'DocumentoRecibo' => array_merge([
                '@attributes' => [
                    'ID' => 'T'.$datos['TipoDoc'].'F'.$datos['Folio'],
                ],
                'TipoDoc' => false,
                'Folio' => false,
                'FchEmis' => false,
                'RUTEmisor' => false,
                'RUTRecep' => false,
                'MntTotal' => false,
                'Recinto' => false,
                'RutFirma' => false,
                'Declaracion' => 'El acuse de recibo que se declara en este acto, de acuerdo a lo dispuesto en la letra b) del Art. 4, y la letra c) del Art. 5 de la Ley 19.983, acredita que la entrega de mercaderias o servicio(s) prestado(s) ha(n) sido recibido(s).',
                'TmstFirmaRecibo' => date('Y-m-d\TH:i:s'),
            ], $datos)
        ];
        return true;
    }

    public function setCaratula(array $caratula)
    {
        $this->caratula = array_merge([
            '@attributes' => [
                'version' => '1.0'
            ],
            'RutResponde' => false,
            'RutRecibe' => false,
            'NmbContacto' => false,
            'FonoContacto' => false,
            'MailContacto' => false,
            'TmstFirmaEnv' => date('Y-m-d\TH:i:s'),
        ], $caratula);
        $this->id = 'SetDteRecibidos';
    }

    public function generar()
    {
        // si ya se había generado se entrega directamente
        if ($this->xml_data)
            return $this->xml_data;
        // si no hay respuestas para generar entregar falso
        if (!isset($this->recibos[0])) {
            Log::write(
                Estado::ENVIORECIBOS_FALTA_RECIBO,
                Estado::get(Estado::ENVIORECIBOS_FALTA_RECIBO)
            );
            return false;
        }
        // si no hay carátula error
        if (!$this->caratula) {
            Log::write(
                Estado::ENVIORECIBOS_FALTA_CARATULA,
                Estado::get(Estado::ENVIORECIBOS_FALTA_CARATULA)
            );
            return false;
        }
        // crear arreglo de lo que se enviará
        $xmlEnvio = (new XML())->generate([
            'EnvioRecibos' => [
                '@attributes' => [
                    'xmlns' => 'http://www.sii.cl/SiiDte',
                    'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                    'xsi:schemaLocation' => 'http://www.sii.cl/SiiDte EnvioRecibos_v10.xsd',
                    'version' => '1.0',
                ],
                'SetRecibos' => [
                    '@attributes' => [
                        'ID' => 'SetDteRecibidos'
                    ],
                    'Caratula' => $this->caratula,
                    'Recibo' => null,
                ]
            ]
        ])->saveXML();
        // generar cada recibo y firmar
        $Recibos = [];
        foreach ($this->recibos as &$recibo) {
            $recibo_xml = new XML();
            $recibo_xml->generate(['Recibo'=>$recibo]);
            $recibo_firmado = $this->Firma ? $this->Firma->signXML($recibo_xml->saveXML(), '#'.$recibo['DocumentoRecibo']['@attributes']['ID'], 'DocumentoRecibo', true) : $recibo_xml->saveXML();
            $Recibos[] = trim(str_replace('<?xml version="1.0" encoding="ISO-8859-1"?>', '', $recibo_firmado));
        }
        // firmar XML del envío y entregar
        $xml = str_replace('<Recibo/>', implode("\n", $Recibos), $xmlEnvio);
        $this->xml_data = $this->Firma ? $this->Firma->signXML($xml, '#SetDteRecibidos', 'SetRecibos', true) : $xml;
        return $this->xml_data;
    }

    public function getID()
    {
        return isset($this->arreglo['EnvioRecibos']['SetRecibos']['@attributes']['ID']) ? $this->arreglo['EnvioRecibos']['SetRecibos']['@attributes']['ID'] : false;
    }

    public function getRecibos()
    {
        // si no hay recibos se deben crear
        if (!$this->recibos) {
            // si no está creado el arrelgo con los datos error
            if (!$this->arreglo) {
                return false;
            }
            // crear recibos a partir del arreglo
            $Recibos = $this->arreglo['EnvioRecibos']['SetRecibos']['Recibo'];
            if (!isset($Recibos[0]))
                $Recibos = [$Recibos];
            foreach ($Recibos as $Recibo) {
                $this->recibos[] = $Recibo;
            }
        }
        // entregar recibos
        return $this->recibos;
    }

}
