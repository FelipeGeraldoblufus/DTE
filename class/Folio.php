<?php 
/**
* 
*/
class Folio extends DataBase
{
	private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaFolio(){
    $consulta = $this->db->query("SELECT folio_actual, desde, hasta, vence, ruta, empresa_rut, tipo_folio FROM folios");

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

  public function newFolio(array $datos) {
    $consulta = $this->db->prepare("INSERT INTO folios values(?,?,?,?,?,?,?)");
    
    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function updFolios($tipo, array $datos) {
    $consulta = $this->db->prepare("UPDATE folios SET folio_actual =?, desde =?, hasta =?, vence =?, ruta =?, empresa_rut =? WHERE tipo_folio = $tipo");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function updFolio($empresaRut, $tipo, $nuevoFolioActual) {
    $consulta = $this->db->prepare("UPDATE folios SET folio_actual = ? WHERE empresa_rut = ? AND tipo_folio = ?");

    try {
        $consulta->execute([$nuevoFolioActual, $empresaRut, $tipo]);
        return true;
    } catch (PDOException $e) {
        // Log del error (opcional)
        error_log("Error al actualizar el folio: " . $e->getMessage());
        return false;
    }
}

  public function delFolio($id) {
    $consulta = $this->db->prepare("DELETE FROM folios WHERE tipo_folio = $id");

    try {
      $consulta->execute();
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function getFolio($id){
    $consulta = $this->db->query("SELECT folio_actual, desde, hasta, vence, ruta, empresa_rut, tipo_folio FROM folios WHERE tipo_folio = $id");

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