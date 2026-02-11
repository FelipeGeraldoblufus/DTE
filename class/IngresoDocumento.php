<?php 
/**
* 
*/
class IngresoDocumento extends DataBase
{
  private $data;
  private $rowCount;
  private $lastInsertId;
  protected $db;

  public function __construct() {
    parent::__construct();
    $this->data = array();
  }

  /**
 * Obtiene todos los documentos de un tipo específico asociados a una empresa
 * 
 * @param string $rutEmpresa El RUT de la empresa
 * @param string $tipoDTE El código del tipo de documento (33, 34, 39, etc.)
 * @return array|bool Devuelve un array con los documentos o false si no hay resultados
 */
public function getDocumentosPorEmpresaYTipo($rutEmpresa, $tipoDTE) {
  $consulta = $this->db->prepare("SELECT id, tipo, dte, folio, emision, vencimiento, 
                               forma_pago, exento, iva, otro_impuesto, 
                               total, cliente_rut, proveedor_rut, ruta_xml, ruta_pdf
                               FROM documento 
                               WHERE empresa_rut = ? AND dte = ? 
                               ORDER BY emision DESC, folio DESC");
  
  $consulta->execute([$rutEmpresa, $tipoDTE]);
  $rowCount = $consulta->rowCount();
  
  if ($rowCount > 0) {
      $data = [];
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
          $data[] = $row;
      }
      return $data;
  } else {
      return false;
  }
}

  public function listaDocumento($id){
    $consulta = $this->db->query("SELECT tipo,dte,folio,emision,vencimiento,total,cliente_rut,proveedor_rut,ruta_xml,ruta_pdf FROM documento WHERE tipo='$id'");

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

  // Create an instance of IngresoDocumento class that extends or includes this fix:
public function newDocumento(array $datos) {
  $consulta = $this->db->prepare("INSERT INTO documento values(null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
  
  try {
      // Bind each parameter separately instead of passing the entire array
      $consulta->bindParam(1, $datos[0], PDO::PARAM_STR);
      $consulta->bindParam(2, $datos[1], PDO::PARAM_INT);
      $consulta->bindParam(3, $datos[2], PDO::PARAM_INT);
      $consulta->bindParam(4, $datos[3], PDO::PARAM_STR);
      $consulta->bindParam(5, $datos[4], PDO::PARAM_STR);
      $consulta->bindParam(6, $datos[5], PDO::PARAM_STR);
      $consulta->bindParam(7, $datos[6], PDO::PARAM_STR);
      $consulta->bindParam(8, $datos[7], PDO::PARAM_STR);
      $consulta->bindParam(9, $datos[8], PDO::PARAM_STR);
      $consulta->bindParam(10, $datos[9], PDO::PARAM_STR);
      $consulta->bindParam(11, $datos[10], PDO::PARAM_STR);
      $consulta->bindParam(12, $datos[11], PDO::PARAM_STR);
      $consulta->bindParam(13, $datos[12], PDO::PARAM_STR);
      $consulta->bindParam(14, $datos[13], PDO::PARAM_STR);
      $consulta->bindParam(15, $datos[14], PDO::PARAM_STR);
      $consulta->bindParam(16, $datos[15], PDO::PARAM_STR);
      
      // Execute without parameters
      $consulta->execute();
      $_SESSION['id_doc'] = $this->db->lastInsertId();
      return true;
  } catch (PDOException $e) {
      return $e;
  }
}
public function newDetalleDocumento(array $datos) {
  $consulta = $this->db->prepare("INSERT INTO detalle_documento 
      (documento_id, codigo_producto, codigo_servicio, cantidad, precio, descuento, total) 
      VALUES (?,?,?,?,?,?,?)");

  try {
      $consulta->execute($datos);
      return true;   
  } catch (PDOException $e) {
      return $e;
  }
}

/**
 * Obtiene todos los documentos asociados a una empresa (por su RUT)
 * 
 * @param string $rutEmpresa El RUT de la empresa
 * @return array|bool Devuelve un array con los documentos o false si no hay resultados
 */
public function getDocumentosPorEmpresa($rutEmpresa) {
  // Modificamos la consulta para eliminar la columna desc_documento que no existe
  $consulta = $this->db->prepare("SELECT id, tipo, dte, folio, emision, vencimiento, 
                                 forma_pago, exento, iva, otro_impuesto, 
                                 total, cliente_rut, proveedor_rut, ruta_xml, ruta_pdf
                                 FROM documento 
                                 WHERE empresa_rut = ? 
                                 ORDER BY emision DESC, folio DESC");
  
  $consulta->execute([$rutEmpresa]);
  $rowCount = $consulta->rowCount();
  
  if ($rowCount > 0) {
      $data = [];
      while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
          $data[] = $row;
      }
      return $data;
  } else {
      return false;
  }
}

/**
 * Obtiene documentos con sus detalles asociados a una empresa por su RUT
 *
 * @param string $rutEmpresa El RUT de la empresa
 * @return array|bool Devuelve un array con los documentos y sus detalles o false si no hay resultados
 */
public function getDocumentosConDetallesPorEmpresa($rutEmpresa) {
    $consulta = $this->db->prepare("SELECT * FROM documento AS doc
                                  INNER JOIN detalle_documento AS det ON det.documento_id = doc.id
                                  WHERE doc.empresa_rut = ?");
    
    $consulta->execute([$rutEmpresa]);
    $rowCount = $consulta->rowCount();
    
    if ($rowCount > 0) {
        $data = [];
        while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    } else {
        return false;
    }
}

  public function newPagoDocumento(array $datos) {
    $consulta = $this->db->prepare("INSERT INTO pago_documento values(null,?,?,?,?)");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function newReferenciaDocumento(array $datos) {
    $consulta = $this->db->prepare("INSERT INTO referencia values(null,?,?,?,?,?)");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function updDocumento($id, array $datos) {
    $consulta = $this->db->prepare("UPDATE documento SET tipo=?,dte=?,folio=?,emision=?,vencimiento=?,descuento=?,exento=?,iva=?,otro_impuesto=?,total=?,cliente_rut=?,proveedor_rut=? WHERE id = $id");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function updDetalleDocumento($id, array $datos) {
    $consulta = $this->db->prepare("UPDATE detalle_documento SET codigo_producto=?,codigo_servicio=?,cantidad=?,precio=?,descuento=?,total=? WHERE id = $id");

    try {
      $consulta->execute($datos);
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function delDocumento($id) {
    $consulta = $this->db->prepare("DELETE FROM documento WHERE id = $id");

    try {
      $consulta->execute();
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function delDetalleDocumento($id) {
    $consulta = $this->db->prepare("DELETE FROM detalle_documento WHERE id = $id");

    try {
      $consulta->execute();
      #$this->closeDB();
      return true;   
    } catch (PDOException $e) {
      #$this->closeDB();
      return false;
    }
  }

  public function getDocumento($id){
    $consulta = $this->db->query("SELECT tipo,dte,folio,emision,vencimiento,total,cliente_rut,proveedor_rut, empresa_rut, ruta_xml, ruta_pdf FROM documento WHERE id = $id");

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

  public function getDetalleDocumento($id){
    $consulta = $this->db->query("SELECT codigo_producto,codigo_servicio,cantidad,precio,descuento,total FROM detalle_documento WHERE documento_id = $id");

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