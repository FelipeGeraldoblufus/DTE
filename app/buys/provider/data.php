<?php 
require_once '../../../config.php';

$Proveedor = new Proveedor();

if (isset($_POST['data']) && !empty($_POST['data'])) {
	switch ($_POST['data']) {
		case 'create':

		if (!empty($_POST['rut'])) {
			$rut = str_replace('.','',$_POST['rut']);
			$datos = [
				$rut,
				!empty($_POST['rznsoc']) ? $_POST['rznsoc'] : '',
				!empty($_POST['numid']) ? $_POST['numid'] : null,
				!empty($_POST['nacionalidad']) ? $_POST['nacionalidad'] : null,
				!empty($_POST['giro']) ? $_POST['giro'] : '',
				!empty($_POST['contacto']) ? $_POST['contacto'] : '',
				!empty($_POST['correo']) ? $_POST['correo'] : '',
				!empty($_POST['direccion']) ? $_POST['direccion'] : '',
				!empty($_POST['comuna']) ? $_POST['comuna'] : '',
				!empty($_POST['ciudad']) ? $_POST['ciudad'] : '',
				!empty($_POST['direccionpostal']) ? $_POST['direccionpostal'] : null,
				!empty($_POST['comunapostal']) ? $_POST['comunapostal'] : null,
				!empty($_POST['ciudadpostal']) ? $_POST['ciudadpostal'] : null
			];

			$Proveedor->newProveedor($datos);			
		}
		break;

		case 'update':
		if (!empty($_POST['rut_edit'])) {
			$rut = str_replace(',','',$_POST['rut_edit']);
			$datos = [
				!empty($_POST['rznsoc_edit']) ? $_POST['rznsoc_edit'] : '',
				!empty($_POST['numid_edit']) ? $_POST['numid_edit'] : null,
				!empty($_POST['nacionalidad_edit']) ? $_POST['nacionalidad_edit'] : null,
				!empty($_POST['giro_edit']) ? $_POST['giro_edit'] : '',
				!empty($_POST['contacto_edit']) ? $_POST['contacto_edit'] : '',
				!empty($_POST['correo_edit']) ? $_POST['correo_edit'] : '',
				!empty($_POST['direccion_edit']) ? $_POST['direccion_edit'] : '',
				!empty($_POST['comuna_edit']) ? $_POST['comuna_edit'] : '',
				!empty($_POST['ciudad_edit']) ? $_POST['ciudad_edit'] : '',
				!empty($_POST['direccionpostal_edit']) ? $_POST['direccionpostal_edit'] : null,
				!empty($_POST['comunapostal_edit']) ? $_POST['comunapostal_edit'] : null,
				!empty($_POST['ciudadpostal_edit']) ? $_POST['ciudadpostal_edit'] : null
			];

			$Proveedor->updProveedor($rut,$datos);
		}
		
		break;

		case 'delete':
    $id = $_POST['id'];

    $Proveedor->delProveedor($id);
    break;
	}
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
  $datos = $Proveedor->getProveedor($_GET['id']);
  
  echo json_encode($datos);
}