<?php 
/**
* 
*/
class Firma extends DataBase
{
	private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  public function listaFirma(){
    $consulta = $this->db->query("SELECT id, rut, nombre, fecha_desde, fecha_hasta, ruta FROM firma");

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

  public function newFirma($rut,$nombre,$desde,$hasta,$ruta,$pass) {
    $consulta = $this->db->prepare("INSERT INTO firma values(null,'$rut','$nombre','$desde','$hasta','$ruta','$pass')");

    
    if ($consulta->execute()) {
        return true;
      } else {
        printf('ERROR en la Query: %s\n', $this->db->error);
      }
  }
  public function getFirmaByRut($rut)
  {
      $consulta = $this->db->query("SELECT rut, nombre, fecha_desde, fecha_hasta, ruta, pass FROM firma WHERE rut = '$rut'");

      $rowCount = $consulta->rowCount();

      if ($rowCount > 0) {
          $data = $consulta->fetch(PDO::FETCH_ASSOC);
          $this->closeDB();
          return $data;
      }
      
      $this->closeDB();
      return false;
  }

  public function updFirma($id, array $datos) {
    $consulta = $this->db->prepare("UPDATE firma SET rut=?, nombre=?, fecha_desde=?, fecha_hasta=?, ruta=? WHERE id = $id");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function delFirma($id) {
    $consulta = $this->db->prepare("DELETE FROM firma WHERE id = $id");

    try {
      $consulta->execute();
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function getFirma($id){
    $consulta = $this->db->query("SELECT id, rut, nombre, fecha_desde, fecha_hasta, ruta FROM firma WHERE id = $id");

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

  public function rutFirma($rut){
    $consulta = $this->db->query("SELECT id, rut, nombre, fecha_desde, fecha_hasta, ruta FROM firma WHERE rut = '$rut'");
    
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

  public function addFirmaUsuario($rut, $id) {
    try {
        // Primero verificar si el usuario existe
        $checkUser = $this->db->prepare("SELECT rut FROM usuario WHERE rut = :rut");
        $checkUser->execute([':rut' => $rut]);
        
        if ($checkUser->rowCount() === 0) {
            // El usuario no existe
            throw new PDOException("El usuario con RUT $rut no existe en la tabla usuario");
        }

        // Si el usuario existe, proceder con la inserción
        $consulta = $this->db->prepare("INSERT INTO firma_usuario (usuario_rut, firma_id) VALUES (:rut, :id)");
        
        return $consulta->execute([
            ':rut' => $rut,
            ':id' => $id
        ]);

    } catch (PDOException $e) {
        // Aquí podrías loggear el error específico si lo necesitas
        // error_log($e->getMessage());
        return false;
    }
}

  public function listaFirmaUsuario($id){
    $consulta = $this->db->query("SELECT usuario_rut, firma_id FROM firma_usuario WHERE firma_id = $id");

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