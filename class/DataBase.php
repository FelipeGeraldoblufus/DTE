<?php

class DataBase
{

  protected $db;

  public function __construct(){
    $dsn = 'mysql: host='.DB_HOST.';dbname='.DB_NAME;

    $options = array(
      PDO::ATTR_PERSISTENT            => true,
      PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
      PDO::MYSQL_ATTR_INIT_COMMAND    => 'SET NAMES '.DB_CHARSET
    );

    try{

      $this->db = new PDO($dsn, DB_USER, DB_PASS, $options);

    } catch(PDOException $e){

      echo "ERROR!! al conectar a la Base de Datos: " .$e->getMessage();
      exit;
    }
  }

  public function closeDB() {
    $this->db = null;
  }
}