<?php
require_once '../../config.php';
$_SESSION['navMenu']	=	'company';
$Empresa = new Empresa();

$rut 				= $_POST['rut'];
$rznsoc 		= $_POST['RznSoc'];
$direccion	= $_POST['DirOrigen'];
$comuna 		= $_POST['CmnaOrigen'];
$ciudad 		= $_POST['CiudadOrigen'];
$giro 			= $_POST['GiroEmis'];
$acteco 		= $_POST['acteco'];
$telefono 	= $_POST['Telefono'];
$correo 		= $_POST['CorreoEmisor'];
$fchresol		= $_POST['fchresol'];
$nroresol		= $_POST['nroresol'];

if (!empty($_FILES["logo"]["name"])) {
	$Directorio = new Directorio();
	$FileManager = new FileManager();

	$carpeta = $Directorio->creaDirectorio('../../client/logo');
	$logo = str_replace('../', '', $carpeta.$rut.'.png');

	$FileManager->setDestination($carpeta);
	$FileManager->setAllowedExtensions('image/png');
	$FileManager->setFileName($rut.'.png');
	$FileManager->upload($_FILES['logo']);
} else {
	$logo = $_POST['logo_actual'];
}

//$updEmpresa = $Empresa->updEmpresa($rut,$rznsoc,$giro,$telefono,$correo,$acteco,$direccion,$comuna,$ciudad,$logo,$fchresol,$nroresol);

$datos = [
    $rznsoc,
    $giro,
    $telefono,
    $correo,
    $acteco,
    $direccion,
    $comuna,
    $ciudad,
    $logo,
    $fchresol,
    $nroresol
];

$updEmpresa = $Empresa->updEmpresa($rut, $datos);

if ($updEmpresa) {
	header('location: index.php');
} else {
	header('location: index.php');
}