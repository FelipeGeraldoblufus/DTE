<?php 
require_once 'config.php';
unset($_SESSION['status']);
unset($_SESSION['nombre_usuario']);
unset($_SESSION['foto_usuario']);
unset($_SESSION['cargo_usuario']);
unset($_SESSION['navMenu']);
if (isset($_SESSION)) {
	session_destroy();
	header('Location:'.$baseurl);
} else {
	header('Location:'.$baseurl);
}
?>