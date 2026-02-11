<?php
require_once '../../../config.php';

$tf = $_POST['tipoFolio'];

$Folios = new Folios(file_get_contents($_FILES['archivo']['tmp_name']));
$TipoFolio = new TipoFolio();
$Directorio = new Directorio();
$FolioManager = new FolioManager();
$Empresa = new Empresa();
$Folio = new Folio();

$datos = $Empresa->listaEmpresa();

if ($Folios->getTipo() == $tf) {
	$tipo = $TipoFolio->idTipoFolio($tf);
	$tipo = $tipo['tipo_numero'];
} else {
	echo 'ERROR! problema con el Folio subido';
}

if ($Folios->getEmisor() == $datos['rut']) {
	$rut = $Folios->getEmisor();
} else {
	echo "ERROR! R.U.T. de la empresa no coincide";
}

$carpeta = $Directorio->creaDirectorio('../../../client/folio/'.$tf);

$actual = $Folios->getDesde();
$desde 	= $Folios->getDesde();
$hasta 	= $Folios->getHasta();
$vence 	= $Folios->getFecha();
$ruta 	= str_replace('../', '', $carpeta.$tf.'.xml');

$FolioManager->setDestination($carpeta);
$FolioManager->setFileName($tf.'.xml');
$FolioManager->upload($_FILES['archivo']);

$updFolio = $Folio->updFolio($actual,$desde,$hasta,$vence,$ruta,$rut,$tipo);

if ($updFolio) {
	echo "El folio se a subido correctamente";
} else {
	echo "ERROR! no se ha guardado la informacion";
}

?>