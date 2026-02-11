<?php 
/**
* 
*/
class Empleado extends DataBase
{
  private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaEmpleado(){
    $consulta = $this->db->query("SELECT rut, nombre, apellido, direccion, comuna, telefono, email, foto, empresa_rut FROM empleado");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
      }

      ##$this->closeDB();
      return $data;
    } else {

      #$this->closeDB();
      return false;
    }
  }

  public function newEmpleado(array $datos) {
    $consulta = $this->db->prepare("INSERT INTO empleado values(?,?,?,?,?,?,?,?,?)");
    
    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function updEmpleado($rut, array $datos) {
    $consulta = $this->db->prepare("UPDATE empleado SET nombre = ?, apellido= ?, direccion = ?, comuna = ?, telefono = ?, email = ?, foto = ? WHERE rut = '$rut'");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function delEmpleado($rut) {
    $consulta = $this->db->prepare("DELETE FROM empleado WHERE rut = '$rut'");

    try {
      $consulta->execute();
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function getEmpleado($rut){
    $consulta = $this->db->query("SELECT rut, nombre, apellido, direccion, comuna, telefono, email, foto, empresa_rut FROM empleado WHERE rut='$rut'");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data = $row;
      }

      #$this->closeDB();
      return $data;
    } else {

      #$this->closeDB();
      return false;
    }
  }
}