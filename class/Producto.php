<?php 
/**
* 
*/
class Producto extends DataBase
{
	private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaProducto(){
    $consulta = $this->db->query("SELECT codigo, nombre, precio, descripcion, unimed FROM producto");

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

  public function newProducto(array $datos) {
    $consulta = $this->db->prepare("INSERT INTO producto values(?,?,?,?,?)");
    
    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function updProducto($codigo,array $datos) {
    $consulta = $this->db->prepare("UPDATE producto SET nombre=?, precio=?, descripcion=?, unimed=? WHERE codigo = '$codigo'");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function delProducto($codigo) {
    $consulta = $this->db->prepare("DELETE FROM producto WHERE codigo = '$codigo'");

    try {
      $consulta->execute();
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function getProducto($codigo){
    $consulta = $this->db->query("SELECT codigo, nombre, precio, descripcion, unimed FROM producto WHERE codigo = '$codigo'");

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

  public function buscaProducto($codigo){
    $consulta = $this->db->query("SELECT codigo FROM producto WHERE codigo LIKE '%$codigo%'");

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

  public function codigoProducto(){
    $consulta = $this->db->query("SELECT codigo, nombre FROM producto");

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