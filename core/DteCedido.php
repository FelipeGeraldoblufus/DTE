<?php
/**
 * Clase que representa el DTE cedido
 */
class DteCedido
{

    private $dte; ///< Objeto con el DTE que se está cediendo
    private $xml; ///< String con el XML del DTE cedido

    public function __construct(Dte $DTE)
    {
        $this->dte = $DTE;
        $xml = (new XML())->generate([
            'DTECedido' => [
                '@attributes' => [
                    'xmlns' => 'http://www.sii.cl/SiiDte',
                    'version' => '1.0'
                ],
                'DocumentoDTECedido' => [
                    '@attributes' => [
                        'ID' => 'DTECedido'
                    ],
                    'DTE' => null,
                    'ImagenDTE' => false,
                    'Recibo' => false,
                    'TmstFirma' => date('Y-m-d\TH:i:s'),
                ]
            ]
        ])->saveXML();
        $xml_dte = $this->dte->saveXML();
        $xml_dte = substr($xml_dte, strpos($xml_dte, '<DTE'));
        $this->xml = str_replace('<DTE/>', $xml_dte, $xml);
    }

    public function firmar(FirmaElectronica $Firma)
    {
        $xml = $Firma->signXML($this->xml, '#DTECedido', 'DocumentoDTECedido');
        if (!$xml) {
            Log::write(
                Estado::DTE_ERROR_FIRMA,
                Estado::get(Estado::DTE_ERROR_FIRMA, '#DTECedido')
            );
            return false;
        }
        $this->xml = $xml;
        return true;
    }

    public function saveXML()
    {
        return $this->xml;
    }

    public function schemaValidate()
    {
        return true;
    }

    public function getDTE()
    {
        return $this->dte;
    }

}
