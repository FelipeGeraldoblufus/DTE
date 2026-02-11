<?php
/**
 * Clase base para los libros XML
 */
abstract class Libro extends Envio
{

    protected $detalles = []; ///< Arreglos con los detalles del documento
    protected $resumen = []; ///< resumenes del libro

    abstract public function agregar(array $detalle);

    public function cantidad()
    {
        return count($this->detalles);
    }

}
