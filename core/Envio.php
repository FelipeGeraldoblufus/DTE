<?php
/**
 * Clase base para los documentos XML
 */
abstract class Envio extends Documento
{
    public function enviar()
    {
        // generar XML que se enviará
        if (!$this->xml_data)
            $this->xml_data = $this->generar();
        if (!$this->xml_data) {
            Log::write(
                Estado::DOCUMENTO_ERROR_GENERAR_XML,
                Estado::get(
                    Estado::DOCUMENTO_ERROR_GENERAR_XML,
                    substr(get_class($this), strrpos(get_class($this), '\\')+1)
                )
            );
            return false;
        }
        // validar schema del documento antes de enviar
        if (!$this->schemaValidate())
            return false;
        // solicitar token
        $token = Autenticacion::getToken($this->Firma);
        if (!$token)
            return false;
        // enviar DTE
        $envia = $this->caratula['RutEnvia']; 
        $emisor = !empty($this->caratula['RutEmisor']) ? $this->caratula['RutEmisor'] : $this->caratula['RutEmisorLibro'];
        $result = Sii::enviar($envia, $emisor, $this->xml_data, $token);
        if ($result===false)
            return false;
        if (!is_numeric((string)$result->TRACKID))
            return false;
        return (int)(string)$result->TRACKID;
    }

}
