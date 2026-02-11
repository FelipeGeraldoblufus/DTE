<?php 
/**
* 
*/
class Directorio
{
	public function creaDirectorio($carpeta)
	{
		if (!file_exists($carpeta))
		{
		  mkdir($carpeta, 0777, true);
		}
		$dir = $carpeta.'/';
		return $dir;
	}
}