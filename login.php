<?php
require_once 'config.php';

$empresa = str_replace('.', '', $_POST['empresa']);
$usuario = str_replace('.', '', $_POST['usuario']);
$password = $_POST['password'];
$Login = new Login();
$Permiso = new Permiso();
$result = $Login->Conectar($usuario, $empresa);

if ($result) {
	if(password_verify($password, $result['password'])) {
		$_SESSION['status']='OK';
		$_SESSION['nombre_usuario']	=	$result['nombre'].' '.$result['apellido'];
		$_SESSION['foto_usuario']	=	BASEURL.$result['foto'];
		$_SESSION['cargo_usuario']	=	$Permiso->perfilPermiso($result['permiso_id']);
		$_SESSION['rut_empresa'] = $empresa;
		$_SESSION['navMenu']	=	'home';
		header('Location: home.php');
	} else {
		header('Location: index.php');
	}
}else{
	header('Location: index.php');
}
?>