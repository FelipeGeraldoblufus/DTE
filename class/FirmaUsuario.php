<?php 
/**
* 
*/
class FirmaUsuario extends DataBase
{
	private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaFirma($id){
    $consulta = $this->db->query("SELECT usuario_rut, firma_id FROM firma_usuario WHERE firma_id = $id");

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

  public function listaUsuario($rut){
    $consulta = $this->db->query("SELECT usuario_rut, firma_id FROM firma_usuario WHERE usuario_rut='$rut'");

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

  public function newFirmaUsuario($rut, $id) {
    $consulta = $this->db->query("INSERT INTO firma_usuario values('$rut', $id)");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function delFirmaUsuario($rut, $id) {
    $consulta = $this->db->query("DELETE FROM firma_usuario values WHERE usuario_rut='$rut' AND firma_id=$id)");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }
}

?>