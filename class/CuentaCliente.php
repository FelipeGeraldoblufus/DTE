<?php 
/**
* 
*/
class CuentaCliente extends DataBase
{
	private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaCuenta($rut){
    $consulta = $this->db->query("SELECT id,nombre,numero,banco_id,tipo_cuenta_id FROM cuenta_cliente WHERE cliente_rut = '$rut' ORDER BY banco_id");

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

  public function newCuenta(array $datos) {
    $consulta = $this->db->prepare("INSERT INTO cuenta_cliente values(null,?,?,?,?,?)");
    
    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function updCuenta($id,array $datos) {
    $consulta = $this->db->prepare("UPDATE cuenta_cliente SET nombre=?, numero=?, banco_id=?, tipo_cuenta_id=? WHERE id=$id");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function delCuenta($id) {
    $consulta = $this->db->prepare("DELETE FROM cuenta_cliente WHERE id=$id");

    try {
      $consulta->execute();
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function getCuenta($id){
    $consulta = $this->db->query("SELECT id,nombre,numero,banco_id,tipo_cuenta_id FROM cuenta_cliente WHERE id=$id");

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

?>