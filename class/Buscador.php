<?php 
/**
* 
*/
class Buscador extends DataBase
{
	public $mysqli;
  public $data;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function buscarCliente($busqueda){
    $consulta = $this->db->query("SELECT * FROM cliente WHERE rznsoc LIKE '%$busqueda%' OR rut LIKE '%$busqueda%'");

    if (!$consulta) {
      printf('ERROR en la Query: %s\n', $this->db->error);
    }

    $numRows = $consulta->num_rows;

    if ($numRows > 0) {
      while ($fila = $consulta->fetch_assoc()) {
        $data[] = $fila;
      }
    }

    if (isset($data)) {
      return $data;
    }
  }

  public function buscarProveedor($busqueda){
    $consulta = $this->db->query("SELECT * FROM proveedor WHERE nombre LIKE %'$busqueda'%");

    if (!$consulta) {
      printf('ERROR en la Query: %s\n', $this->db->error);
    }

    $numRows = $consulta->num_rows;

    if ($numRows > 0) {
      while ($fila = $consulta->fetch_assoc()) {
        $data[] = $fila;
      }
    }

    if (isset($data)) {
      return $data;
    }
  }
}