<?php 
/**
* 
*/
class Deuda extends DataBase
{
	private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaDeuda(){
    $consulta = $this->db->query("SELECT id,documento,num_doc,emision,vencimiento,monto,cliente_rut FROM deuda ORDER BY emision");

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

  public function newDeuda($documento,$num_doc,$emision,$vencimiento,$monto,$cliente_rut) {
    $consulta = $this->db->query("INSERT INTO deuda values(null,'$documento',$num_doc,'$emision','$vencimiento',$monto,'$cliente_rut')");
    
    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function updDeuda($id,$documento,$num_doc,$emision,$vencimiento,$monto,$cliente_rut) {
    $consulta = $this->db->query("UPDATE deuda SET documento='$documento' ,num_doc=$num_doc ,emision='$emision' ,vencimiento='$vencimiento' ,monto=$monto ,cliente_rut='$cliente_rut' WHERE id=$id");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function delDeuda($id) {
    $consulta = $this->db->query("DELETE FROM deuda WHERE id=$id");

    try {
      $consulta->execute();
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function getDeuda($id){
    $consulta = $this->db->query("SELECT id,documento,num_doc,emision,vencimiento,monto,cliente_rut FROM deuda WHERE id=$id");

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

  public function deudaCliente($cliente_rut){
    $consulta = $this->db->query("SELECT id,documento,num_doc,emision,vencimiento,monto,cliente_rut FROM deuda WHERE cliente_rut='$cliente_rut' ORDER BY emision");

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