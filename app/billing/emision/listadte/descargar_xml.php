<?php
require_once '../../../../config.php';

// Verificar que se haya proporcionado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: ID de documento no proporcionado');
}

$idDocumento = intval($_GET['id']);

// Obtener información del documento
$Documentos = new IngresoDocumento();
$documento = $Documentos->getDocumento($idDocumento);

if (!$documento) {
    die('Error: El documento solicitado no existe');
}

// Verificar si existe la ruta del XML
if (!isset($documento['ruta_xml']) || empty($documento['ruta_xml'])) {
    die('Error: Este documento no tiene un archivo XML asociado');
}

// Usar la ruta tal como está almacenada en la base de datos
$rutaArchivo = $documento['ruta_xml'];

// Verificar si el archivo existe
if (!file_exists($rutaArchivo)) {
    die('Error: El archivo XML no se encuentra en el servidor (Ruta: ' . $rutaArchivo . ')');
}

// Obtener información del archivo
$filename = basename($rutaArchivo);
$filesize = filesize($rutaArchivo);

// Definir headers para descarga
header('Content-Description: File Transfer');
header('Content-Type: application/xml');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . $filesize);

// Limpiar cualquier salida anterior
ob_clean();
flush();

// Leer el archivo y enviarlo al navegador
readfile($rutaArchivo);
exit;
?>