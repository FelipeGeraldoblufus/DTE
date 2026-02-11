<?php 
/**
* 
*/
class Usuario extends DataBase
{
	private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaUsuario(){
    $consulta = $this->db->query("SELECT rut, nombre, apellido, direccion, comuna, telefono, email, foto, permiso_id, empresa_rut FROM usuario");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
      }

      #$this->closeDB();
      return $data;
    } else {

      $this->closeDB();
      return false;
    }
  }

  public function newUsuario(array $datos) {
    $consulta = $this->db->prepare("INSERT INTO usuario values(?,?,?,?,?,?,?,?,?,?,?)");
    
    try {
      $consulta->execute($datos);
      $this->closeDB();
      return true;   
    } catch (PDOException $e) {
      $this->closeDB();
      return false;
    }
  }

  public function updUsuario($rut, array $datos) {
    $consulta = $this->db->prepare("UPDATE usuario SET nombre = ?, apellido= ?, direccion = ?, comuna = ?, telefono = ?, email = ?, foto = ?, permiso_id = ? WHERE rut = '$rut'");

    try {
      $consulta->execute($datos);
      $this->closeDB();
      return true;   
    } catch (PDOException $e) {
      $this->closeDB();
      return false;
    }
  }

  public function delUsuario($rut) {
    $consulta = $this->db->prepare("DELETE FROM usuario WHERE rut = '$rut'");

    try {
      $consulta->execute();
      $this->closeDB();
      return true;   
    } catch (PDOException $e) {
      $this->closeDB();
      return false;
    }
  }

  public function changePassword($rut,$password) {
    $consulta = $this->db->prepare("UPDATE usuario SET password='$password' WHERE rut = '$rut'");

    try {
      $consulta->execute($datos);
      $this->closeDB();
      return true;   
    } catch (PDOException $e) {
      $this->closeDB();
      return false;
    }
  }

  public function getUsuario($rut){
    $consulta = $this->db->query("SELECT rut, nombre, apellido, direccion, comuna, telefono, email, foto, permiso_id, empresa_rut FROM usuario WHERE rut='$rut'");

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