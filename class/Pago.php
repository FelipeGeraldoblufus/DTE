<?php 
/**
* 
*/
class Pago extends DataBase
{
	private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaPago(){
    $consulta = $this->db->query("SELECT id,pago,monto,fecha,comentario,tipo_pago_id,deuda_id,deuda_cliente_rut FROM pago ORDER BY fecha");

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

  public function addPago($pago,$monto,$fecha,$comentario,$tipo_pago_id,$deuda_id,$deuda_cliente_rut) {
    $consulta = $this->db->query("INSERT INTO pago values(null,'$pago',$monto,'$fecha','$comentario',$tipo_pago_id,$deuda_id,'$deuda_cliente_rut')");
    
    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function updPago($id,$pago,$monto,$fecha,$comentario,$tipo_pago_id,$deuda_id,$deuda_cliente_rut) {
    $consulta = $this->db->query("UPDATE pago SET $pago='$pago',monto=$monto, fecha='$fecha', comentario='$comentario', tipo_pago_id=$tipo_pago_id, deuda_id=$deuda_id, deuda_cliente_rut='$deuda_cliente_rut' WHERE id=$id");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function delPago($id) {
    $consulta = $this->db->query("DELETE FROM pago WHERE id=$id");

    try {
      $consulta->execute();
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function idPago($id){
    $consulta = $this->db->query("SELECT id,pago,monto,fecha,comentario,tipo_pago_id,deuda_id,deuda_cliente_rut FROM pago WHERE id=$id");

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

  public function pagoCliente($cliente_rut){
    $consulta = $this->db->query("SELECT id,pago,monto,fecha,comentario,tipo_pago_id,deuda_id,deuda_cliente_rut FROM pago WHERE deuda_cliente_rut='$cliente_rut' ORDER BY emision");

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