<?php 
/**
* 
*/
class Bodega extends DataBase
{
  private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaBodega(){
    $consulta = $this->db->query("SELECT id, bodega, direccion, telefono FROM bodega");

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

  public function newBodega(array $datos) {
    $consulta = $this->db->prepare("INSERT INTO bodega values(null,?,?,?)");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function updBodega($id, array $datos) {
    $consulta = $this->db->prepare("UPDATE bodega SET bodega = ?, direccion = ?, telefono = ? WHERE id = $id");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function delBodega($id) {
    $consulta = $this->db->prepare("DELETE FROM bodega WHERE id = $id");

    try {
      $consulta->execute();
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function getBodega($id){
    $consulta = $this->db->query("SELECT id, bodega, direccion, telefono FROM bodega WHERE id = $id");

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

  public function nameBodega($id){
    $consulta = $this->db->query("SELECT bodega FROM bodega WHERE id = $id");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data = $row['bodega'];
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