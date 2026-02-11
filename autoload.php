<?php 
  function autoload($class, $dir = null) {

    if (is_null($dir)){
      $dir = dirname(__FILE__);
    }

    if ($class === 'TCPDF') {
      $class = strtolower($class);
    }
 
    foreach (scandir($dir) as $file) {

      if (is_dir($dir.'/'.$file) && substr($file, 0, 1) !== '.' ){
        autoload($class, $dir.'/'.$file);
      }
 
      if (preg_match("/.php$/i" , $file) ) {
        if (str_replace('.php', '', $file) == $class) {
          require_once $dir.'/'.$file;
        }
      }
    }
  }

  spl_autoload_register('autoload');
?>