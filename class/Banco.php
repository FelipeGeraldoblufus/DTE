<?php 
/**
* 
*/
class Banco extends DataBase
{
	private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaBanco(){
    $consulta = $this->db->query("SELECT id,banco FROM banco");

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

  public function newBanco(array $datos) {
    $consulta = $this->db->prepare("INSERT INTO banco values(null,?)");
    
    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function updBanco($id,array $datos) {
    $consulta = $this->db->prepare("UPDATE banco SET banco=? WHERE id=$id");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function delBanco($id) {
    $consulta = $this->db->prepare("DELETE FROM banco WHERE id=$id");

    try {
      $consulta->execute();
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function getBanco($id){
    $consulta = $this->db->query("SELECT id,banco FROM banco WHERE id=$id");

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

  public function nombreBanco($id){
    $consulta = $this->db->query("SELECT banco FROM banco WHERE id=$id");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data = $row['banco'];
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