<?php 
/**
* 
*/
class Empresa extends DataBase
{
	private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaEmpresa(){
    $consulta = $this->db->query("SELECT rut, rznsoc, giro, telefono, correo, acteco, direccion, comuna, ciudad, logo, fchresol, nroresol, firma  FROM empresa");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data = $row;
      }

      $this->closeDB();
      return $data;
    } else {

      $this->closeDB();
      return false;
    }
  }

  public function getEmpresaRut($rutEmpresa) {
    // Query with WHERE clause to filter by the provided RUT
    $sql = "SELECT rut, rznsoc, giro, telefono, correo, acteco, direccion, comuna, ciudad, logo, fchresol, nroresol, firma
            FROM empresa 
            WHERE rut = :rut";
            
    $consulta = $this->db->prepare($sql);
    $consulta->bindParam(':rut', $rutEmpresa, PDO::PARAM_STR);
    $consulta->execute();
    
    $rowCount = $consulta->rowCount();
    
    if ($rowCount > 0) {
      $data = $consulta->fetch(PDO::FETCH_ASSOC);
      $this->closeDB();
      return $data;
    } else {
      $this->closeDB();
      return false;
    }
  }

  public function rutRazsoc(){
    $consulta = $this->db->query("SELECT rut, rznsoc FROM empresa");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
      }

      $this->closeDB();
      return $data;
    } else {

      $this->closeDB();
      return false;
    }
  }

  public function getEmpresa(){
    $consulta = $this->db->query("SELECT rut, rznsoc, giro, telefono, correo, acteco, direccion, comuna, ciudad, logo, fchresol, nroresol  FROM empresa");

    $rowCount = $consulta->rowCount();

    if ($rowCount > 0) {
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $data = $row;
      }

      $this->closeDB();
      return $data;
    } else {

      $this->closeDB();
      return false;
    }
  }

  public function updEmpresa($rut, array $datos) {
    $consulta = $this->db->query("UPDATE empresa SET rznsoc=?, giro =?, telefono=?, correo=?, acteco=?, direccion=?, comuna=?, ciudad=?, logo=?, fchresol=?, nroresol=?, firma=? WHERE rut = '$rut'");

    try {
      $consulta->execute($datos);
      $this->closeDB();
      return true;   
    } catch (PDOException $e) {
      $this->closeDB();
      return false;
    }
  }
}
?>