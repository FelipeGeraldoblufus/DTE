<?php 
/**
* 
*/
class Cliente extends DataBase
{
	private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaCliente(){
    $consulta = $this->db->query("SELECT rut, rznsoc, numid, nacionalidad, giro, contacto, correo, correo_envio, direccion, comuna, ciudad, direccionpostal, comunapostal, ciudadpostal FROM cliente");

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

  public function newCliente(array $datos) {
    $consulta = $this->db->prepare("INSERT INTO cliente values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    
    try {
      $consulta->execute($datos);
      $this->closeDB();
      return true;   
    } catch (PDOException $e) {
      $this->closeDB();
      return false;
    }
  }

  public function updCliente($rut, array $datos) {
    $consulta = $this->db->prepare("UPDATE cliente SET rznsoc = ?, numid = ?, nacionalidad = ?, giro = ?, contacto = ?, correo = ?, correo_envio = ?, direccion = ?, comuna = ?, ciudad = ?, direccionpostal = ?, comunapostal = ?, ciudadpostal = ? WHERE rut = '$rut'");

    try {
      $consulta->execute($datos);
      $this->closeDB();
      return true;   
    } catch (PDOException $e) {
      $this->closeDB();
      return false;
    }
  }

  public function delCliente($rut) {
    $consulta = $this->db->prepare("DELETE FROM cliente WHERE rut = '$rut'");

    $consulta->execute();
    
    $this->closeDB();
  }

  public function getCliente($rut){
    $consulta = $this->db->query("SELECT rut, rznsoc, numid, nacionalidad, giro, contacto, correo, correo_envio, direccion, comuna, ciudad, direccionpostal, comunapostal, ciudadpostal FROM cliente WHERE rut = '$rut'");

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

  public function buscaCliente($rut){
    $consulta = $this->db->query("SELECT rut FROM cliente WHERE rut LIKE '%$rut%'");

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

  public function rznsocCliente($rut){
    $consulta = $this->db->query("SELECT rznsoc FROM cliente WHERE rut = '$rut'");

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