<?php
require '../../../config.php';

$Usuario = new Usuario();

if (isset($_POST['data']) && !empty($_POST['data'])) {
  switch ($_POST['data']) {
    case 'create':
    if (!empty($_POST['rut'])) {
        $rut = str_replace('.','',$_POST['rut']);
    	$Directorio = new Directorio();
    	$path  = $Directorio->creaDirectorio('../../../client/fotos/usuarios/' . $rut);
    	if ($_FILES['imagen']['name'][0] === '' || $_FILES['imagen']['tmp_name'][0] === '') {
    		$files = array();
    		foreach ($_FILES['foto'] as $k => $l) {
    			foreach ($l as $i => $v) {
    				if (!array_key_exists($i, $files)) {
    					$files[$i] = array();
    				}
    				$files[$i][$k] = $v;
    			}
    		}

    		$FileManager = new FileManager();
    		foreach ($files as $file) {
    			$FileManager->setDestination($path);
    			$FileManager->setAllowedExtensions(array('image/jpeg', 'image/jpg'));
    			$FileManager->setFileName($file['name']);
    			$FileManager->upload($file);
    			$foto = str_replace('../', '', $path . $file['name']);
    		}    		
    	} else {
    		$foto = 'client/fotos/usuarios/default.png';
    	}

      $password = password_hash($_POST['password'], PASSWORD_DEFAULT, array("cost"=>10));

	    $datos = [
	    	$rut,
	    	$password,
	    	!empty($_POST['nombre']) ? $_POST['nombre'] : '',
	    	!empty($_POST['apellido']) ? $_POST['apellido'] : '',
	    	!empty($_POST['direccion']) ? $_POST['direccion'] : '',
	    	!empty($_POST['comuna']) ? $_POST['comuna'] : '',
	    	!empty($_POST['telefono']) ? $_POST['telefono'] : '',
	    	!empty($_POST['email']) ? $_POST['email'] : '',
	    	$foto,
	    	!empty($_POST['permiso']) ? $_POST['permiso'] : '',
	    	!empty($_POST['empresa']) ? $_POST['empresa'] : ''
	    ];

	    $Usuario->newUsuario($datos);
    }
    break;

    case 'update':

    if ($_FILES['foto']['name'][0] === '' || $_FILES['foto']['tmp_name'][0] === '') {
  		$foto = $_POST['imagen_actual'];
    } else {

    	$Directorio = new Directorio();
    	$path  = $Directorio->creaDirectorio('../../../client/fotos/usuarios/'.$_POST['id']);
    	$files = array();
    	foreach ($_FILES['foto'] as $k => $l) {
    		foreach ($l as $i => $v) {
    			if (!array_key_exists($i, $files)) {
    				$files[$i] = array();
    			}
    			$files[$i][$k] = $v;
    		}
    	}

    	$FileManager = new FileManager();
    	foreach ($files as $file) {
    		$FileManager->setDestination($path);
    		$FileManager->setAllowedExtensions(array('image/jpeg', 'image/jpg', 'image/png'));
    		$FileManager->setFileName($file['name']);
    		$FileManager->upload($file);
    		$foto = str_replace('../', '', $path . $file['name']);
    	}  
    }

    $rut = $_POST['id'];

    $datos = [
    	!empty($_POST['nombre_edit']) ? $_POST['nombre_edit'] : '',
    	!empty($_POST['apellido_edit']) ? $_POST['apellido_edit'] : '',
    	!empty($_POST['direccion_edit']) ? $_POST['direccion_edit'] : '',
    	!empty($_POST['comuna_edit']) ? $_POST['comuna_edit'] : '',
    	!empty($_POST['telefono_edit']) ? $_POST['telefono_edit'] : '',
    	!empty($_POST['email_edit']) ? $_POST['email_edit'] : '',
    	$foto,
    	!empty($_POST['permiso_edit']) ? $_POST['permiso_edit'] : ''
    ];

    $Usuario->updUsuario($rut, $datos);

    break;

    case 'delete':
    $id = $_POST['id'];

    break;

    case 'changePassword':
    $rut = $_POST['id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT, array("cost"=>10));

    $Usuario->changePassword($rut,$password);
    break;
  }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
  $datos = $Usuario->getUsuario($_GET['id']);
  
  echo json_encode($datos);
}