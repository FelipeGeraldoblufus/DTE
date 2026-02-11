<?php 
/**
* 
*/
class Permiso extends DataBase
{
	private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaPermiso(){
    $consulta = $this->db->query("SELECT id, permiso FROM permiso");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
      }
      #$this->closeDB();
      return $data;
    } else {
      $this->closeDB();
      return false;
    }
  }

  public function newPermiso(array $datos) {
    $consulta = $this->db->query("INSERT INTO permiso values(null,?)");
    
    try {
      $consulta->execute($datos);
      $this->closeDB();
      return true;   
    } catch (PDOException $e) {
      $this->closeDB();
      return false;
    }
  }

  public function updPermiso($id, array $datos) {
    $consulta = $this->db->query("UPDATE permiso SET permiso = ? WHERE id = $id");

    try {
      $consulta->execute($datos);
      $this->closeDB();
      return true;   
    } catch (PDOException $e) {
      $this->closeDB();
      return false;
    }
  }

  public function delPermiso($id) {
    $consulta = $this->db->query("DELETE FROM permiso WHERE id = $id");

    try {
      $consulta->execute();
      $this->closeDB();
      return true;   
    } catch (PDOException $e) {
      $this->closeDB();
      return false;
    }
  }

  public function getPermiso($id){
    $consulta = $this->db->query("SELECT id, permiso FROM permiso WHERE id = $id");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
      }

      #$this->closeDB();
      return $data;
    } else {
      $this->closeDB();
      return false;
    }
  }

  public function perfilPermiso($id){
    $consulta = $this->db->query("SELECT permiso FROM permiso WHERE id = $id");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data = $row['permiso'];
      }
      #$this->closeDB();
      return $data;
    } else {
      $this->closeDB();
      return false;
    }
  }
}

?>