<?php 
/**
* 
*/
class TipoCuenta extends DataBase
{
  private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaTipoCuenta(){
    $consulta = $this->db->query("SELECT id,tipo FROM tipo_cuenta");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
      }
      #$this->closeDB();
      return $data;
    } else {
      #$this->closeDB();
      return false;
    }
  }

  public function newTipoCuenta(array $datos) {
    $consulta = $this->db->prepare("INSERT INTO tipo_cuenta values(null,?)");
    
    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function updTipoCuenta($id,array $datos) {
    $consulta = $this->db->prepare("UPDATE tipo_cuenta SET tipo=? WHERE id=$id");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function delTipoCuenta($id) {
    $consulta = $this->db->prepare("DELETE FROM tipo_cuenta WHERE id=$id");

    try {
      $consulta->execute();
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function getTipoCuenta($id){
    $consulta = $this->db->query("SELECT id,tipo FROM tipo_cuenta WHERE id=$id");

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

  public function tipoCuenta($id){
    $consulta = $this->db->query("SELECT tipo FROM tipo_cuenta WHERE id=$id");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data = $row['tipo'];
      }
      #$this->closeDB();
      return $data;
    } else {
      #$this->closeDB();
      return false;
    }
  }
}

?>