<?php 
/**
* 
*/
class Proveedor extends DataBase
{
  private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaProveedor(){
    $consulta = $this->db->query("SELECT rut, rznsoc, numid, nacionalidad, giro, contacto, correo, direccion, comuna, ciudad, direccionpostal, comunapostal, ciudadpostal FROM proveedor");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
      }

      $this->closeDB();
      return $data;
    } else {

      $this->closeDB();
      return false;
    }
  }

  public function newProveedor(array $datos) {
    $consulta = $this->db->prepare("INSERT INTO proveedor values(?,?,?,?,?,?,?,?,?,?,?,?,?)");
    
    try {
      $consulta->execute($datos);
      $this->closeDB();
      return true;   
    } catch (PDOException $e) {
      $this->closeDB();
      return false;
    }
  }

  public function updProveedor($rut, array $datos) {
    $consulta = $this->db->prepare("UPDATE proveedor SET rznsoc = ?, numid = ?, nacionalidad = ?, giro = ?, contacto = ?, correo = ?, direccion = ?, comuna = ?, ciudad = ?, direccionpostal = ?, comunapostal = ?, ciudadpostal = ? WHERE rut = '$rut'");

    try {
      $consulta->execute($datos);
      $this->closeDB();
      return true;   
    } catch (PDOException $e) {
      $this->closeDB();
      return false;
    }
  }

  public function delProveedor($rut) {
    $consulta = $this->db->prepare("DELETE FROM proveedor WHERE rut = '$rut'");

    $consulta->execute();
    
    $this->closeDB();
  }

  public function getProveedor($rut){
    $consulta = $this->db->query("SELECT rut, rznsoc, numid, nacionalidad, giro, contacto, correo, direccion, comuna, ciudad, direccionpostal, comunapostal, ciudadpostal FROM proveedor WHERE rut = '$rut'");

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

  public function buscaProveedor($rut){
    $consulta = $this->db->query("SELECT rut FROM proveedor WHERE rut LIKE '%$rut%'");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
      }

      $this->closeDB();
      return $data;
    } else {

      $this->closeDB();
      return false;
    }
  }

  public function rznsocProveedor($rut){
    $consulta = $this->db->query("SELECT rznsoc FROM proveedor WHERE rut = '$rut'");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data = $row['rznsoc'];
      }

      #$this->closeDB();
      return $data;
    } else {

      #$this->closeDB();
      return false;
    }
  }

}