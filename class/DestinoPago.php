<?php 
/**
* 
*/
class DestinoPago extends DataBase
{
	private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaDestinoPago(){
    $consulta = $this->db->query("SELECT id,cuenta_empresa_id,pago_id,pago_tipo_pago_id FROM destino_pago ORDER BY pago_id");

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

  public function newDestinoPago($empresa,$pago,$tipo) {
    $consulta = $this->db->query("INSERT INTO destino_pago values(null,$empresa,$pago,$tipo)");
    
    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function updDestinoPago($id,$empresa,$pago,$tipo) {
    $consulta = $this->db->query("UPDATE destino_pago SET cuenta_empresa_id=$empresa, pago_id=$pago, pago_tipo_pago_id=$tipo WHERE id=$id");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function delDestinoPago($id) {
    $consulta = $this->db->query("DELETE FROM destino_pago WHERE id=$id");

    try {
      $consulta->execute();
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function getDestinoPago($id){
    $consulta = $this->db->query("SELECT id,cuenta_empresa_id,pago_id,pago_tipo_pago_id FROM destino_pago WHERE id=$id");

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