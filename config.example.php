<?php
/**
 * ========================================
 * ARCHIVO DE CONFIGURACIÓN - EJEMPLO
 * ========================================
 * 
 * Este es un archivo de ejemplo para la configuración del sistema DTE.
 * 
 * INSTRUCCIONES DE USO:
 * 1. Copia este archivo y renómbralo a "config.php"
 * 2. Completa las credenciales de la base de datos según tu entorno
 * 3. NUNCA subas config.php al repositorio (está en .gitignore)
 * 
 * ========================================
 */

if (!isset($_SESSION)) {
	session_start();
}

require_once 'autoload.php';

// Zona horaria para Chile
date_default_timezone_set('America/Santiago');

// Configuración de errores (cambiar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Detección de HTTPS
if (isset($_SERVER['HTTPS']) )
{
  define('SSL', 'https://');
}
else
{
  define('SSL', 'http://');
}

// Carpeta base de la aplicación (ej: '/dte' si está en subdirectorio)
define('BASEFOLDER', '');

// URL base de la aplicación
define('BASEURL', SSL.$_SERVER['SERVER_NAME'].BASEFOLDER);

// Directorio raíz del proyecto
define('__ROOT__', dirname(__FILE__));

// ========================================
// CONFIGURACIÓN DE BASE DE DATOS
// ========================================
// IMPORTANTE: Completa estos valores con tus credenciales locales

// Host de la base de datos (normalmente 'localhost' o '127.0.0.1')
define('DB_HOST', 'localhost');

// Usuario de la base de datos
// Ejemplo: define('DB_USER', 'tu_usuario_mysql');
define('DB_USER', '');

// Contraseña de la base de datos
// Ejemplo: define('DB_PASS', 'tu_contraseña_segura');
define('DB_PASS', '');

// Nombre de la base de datos
// Ejemplo: define('DB_NAME', 'dte');
define('DB_NAME', '');

// Puerto de MySQL (opcional, normalmente 3306)
// Ejemplo: define('DB_PORT', '3306');
define('DB_PORT', '');

// Charset de la base de datos
define('DB_CHARSET', 'utf8');

// ========================================

$baseurl = BASEURL;

// Gestión de sesión y token
if (!isset($_SESSION['status'])) {
	$token = md5(uniqid(microtime(), true));
	$_SESSION['status'] = 'OFF';
	$_SESSION['token'] = $token;
} else {
	$_SESSION['status'] = $_SESSION['status'];
}

if ($_SESSION['status'] == 'OFF') {
	if (isset($_SESSION['token'])) {
		unset($_SESSION['token']);
		header('Location:'.$baseurl);
	}
}

?>
