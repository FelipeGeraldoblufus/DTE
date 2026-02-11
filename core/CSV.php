<?php
/**
 * Manejar archivos CSV
 */
class CSV
{
    public static function read($archivo, $separador = ';', $delimitadortexto = '"')
    {
        if (($handle = fopen($archivo, 'r')) !== FALSE) {
            $data = array();
            $i = 0;
            while (($row = fgetcsv($handle, 0, $separador, $delimitadortexto)) !== FALSE) {
                $j = 0;
                foreach ($row as &$col) {
                    $data[$i][$j++] = $col;
                }
                ++$i;
            }
            fclose($handle);
        }
        return $data;
    }

    public static function generate(array $data, $archivo, $separador = ';', $delimitadortexto = '"')
    {
        ob_clean();
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename='.$archivo.'.csv');
        header('Pragma: no-cache');
        header('Expires: 0');
        foreach($data as &$row) {
            foreach($row as &$col) {
                $col = $delimitadortexto.rtrim(str_replace('<br />', ', ', strip_tags($col, '<br>')), " \t\n\r\0\x0B,").$delimitadortexto;
            }
            echo implode($separador, $row),"\r\n";
            unset($row);
        }
        unset($data);
        exit(0);
    }

    public static function save(array $data, $archivo, $separador = ';', $delimitadortexto = '"')
    {
        $fd = fopen($archivo, 'w');
        foreach($data as &$row) {
            foreach($row as &$col) {
                $col = $delimitadortexto.rtrim(str_replace('<br />', ', ', strip_tags($col, '<br>')), " \t\n\r\0\x0B,").$delimitadortexto;
            }
            fwrite($fd, implode($separador, $row)."\r\n");
            unset($row);
        }
        fclose($fd);
    }

}
