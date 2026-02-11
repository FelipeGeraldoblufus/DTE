<?php 
/**
* 
*/
class Stock extends DataBase
{
	private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaStock(){
    $consulta = $this->db->query("SELECT id, stock, bodega_id, producto_codigo FROM stock");

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

  public function newStock(array $datos) {
    $consulta = $this->db->prepare("INSERT INTO stock values(null,?,?,?)");
    
    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function updStock($id, array $datos) {
    $consulta = $this->db->prepare("UPDATE stock SET stock=?, bodega_id=?, producto_codigo=? WHERE id = $id");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function delStock($id) {
    $consulta = $this->db->prepare("DELETE FROM stock WHERE id = $id");

    try {
      $consulta->execute();
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function getStock($id){
    $consulta = $this->db->query("SELECT id, stock, bodega_id, producto_codigo FROM stock WHERE id = $id");

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

  public function stockProducto($codigo){
    $consulta = $this->db->query("SELECT id, stock, bodega_id, producto_codigo FROM stock WHERE producto_codigo = '$codigo'");

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

  public function stockBodega($bodega){
    $consulta = $this->db->query("SELECT id, stock, bodega_id, producto_codigo FROM stock WHERE bodega_id = $bodega");

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
}

?>