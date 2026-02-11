<?php
require_once '../../../config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$Folio = new Folio();
if ($Folio->delFolio($_GET['id'])) {
    header('Location: index.php');
} else {
    die('Error al eliminar el folio');
}
?>