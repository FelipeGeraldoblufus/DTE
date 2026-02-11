<?php 
/**
* 
*/
class Login extends DataBase
{
	public $mysqli;
    public $data;

  function __construct() {
    parent::__construct();
    $this->data = array();
  }

  function Conectar($rut, $empresa)
  {
    $consulta = $this->db->query("SELECT rut, password, nombre, apellido, direccion, comuna, telefono, email, foto, permiso_id, empresa_rut FROM usuario WHERE rut = '$rut' AND empresa_rut = '$empresa'");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data = $row;
      }

      $this->closeDB();
      return $data;
    } else {

      $this->closeDB();
      return false;
    }
  }
}