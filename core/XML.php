<?php
libxml_use_internal_errors(true);
/**
 * Clase para trabajar con XMLs
 */
class XML extends \DomDocument
{

    public function __construct($version = '1.0', $encoding = 'ISO-8859-1')
    {
        parent::__construct($version, $encoding);
        $this->formatOutput = true;
    }

    public function generate(array $array, \DOMElement &$parent = null)
    {
        if ($parent===null)
            $parent = &$this;
        foreach ($array as $key => $value) {
            if ($key=='@attributes') {
                foreach ($value as $attr => $val) {
                    if ($val!==false)
                        $parent->setAttribute($attr, $val);
                }
            } else if ($key=='@value') {
                $parent->nodeValue = $this->sanitize($value);
            } else {
                if (is_array($value)) {
                    if (!empty($value)) {
                        $keys = array_keys($value);
                        if (!is_int($keys[0])) {
                            $value = [$value];
                        }
                        foreach ($value as $value2) {
                            $Node = new \DOMElement($key);
                            $parent->appendChild($Node);
                            $this->generate($value2, $Node);
                        }
                    }
                } else {
                    if (is_object($value) and $value instanceof \DOMElement) {
                        $Node = $this->importNode($value, true);
                        $parent->appendChild($Node);
                    } else {
                        if ($value!==false) {
                            $Node = new \DOMElement($key, $this->iso2utf($this->sanitize($value)));
                            $parent->appendChild($Node);
                        }
                    }
                }
            }
        }
        return $this;
    }
    
    private function sanitize($txt)
    {
        // si no se paso un texto o bien es un número no se hace nada
        if (!$txt || is_numeric($txt)) {
            // Asegurar que los números o valores nulos no tengan saltos de línea
            return trim(strval($txt));
        }
        // convertir "predefined entities" de XML
        $txt = str_replace(
            ['&amp;', '&#38;', '&lt;', '&#60;', '&gt;', '&#62', '&quot;', '&#34;', '&apos;', '&#39;'],
            ['&', '&', '<', '<', '>', '>', '"', '"', '\'', '\''],
            $txt
        );
        $txt = str_replace('&', '&amp;', $txt);
        return $txt;
    }

    public function loadXML($source, $options = null)
    {
        return $source ? parent::loadXML($this->iso2utf($source), $options) : false;
    }

    public function xpath($expression)
    {
        return (new \DOMXPath($this))->query($expression);
    }

    public function getFlattened($xpath = null)
    {
        if ($xpath) {
            $node = $this->xpath($xpath)->item(0);
            if (!$node)
                return false;
            $xml = $this->utf2iso($node->C14N());
            $xml = $this->fixEntities($xml);
        } else {
            $xml = $this->C14N();
        }
        $xml = preg_replace("/\>\n\s+\</", '><', $xml);
        $xml = preg_replace("/\>\n\t+\</", '><', $xml);
        $xml = preg_replace("/\>\n+\</", '><', $xml);
        return trim($xml);
    }

    private function utf2iso($string)
    {
        return mb_detect_encoding($string, ['UTF-8', 'ISO-8859-1']) != 'ISO-8859-1' ? utf8_decode($string) : $string;
    }

    private function iso2utf($string)
    {
        return $string;
        //return mb_detect_encoding($string, ['ISO-8859-1', 'UTF-8']) == 'ISO-8859-1' ? utf8_encode($string) : $string;
    }

    public function toArray(\DOMElement $dom = null, array &$array = null, $arregloNodos = false)
    {
        // determinar valores de parámetros
        if (!$dom)
            $dom = $this->documentElement;
        if (!$dom)
            return false;
        if ($array===null)
            $array = [$dom->tagName => null];
        // agregar atributos del nodo
        if ($dom->hasAttributes()) {
            $array[$dom->tagName]['@attributes'] = [];
            foreach ($dom->attributes as $attribute) {
                $array[$dom->tagName]['@attributes'][$attribute->name] = $attribute->value;
            }
        }
        // agregar nodos hijos
        if ($dom->hasChildNodes()) {
            foreach($dom->childNodes as $child) {
                if ($child instanceof \DOMText) {
                    $textContent = trim($child->textContent);
                    if ($textContent!="") {
                        if ($dom->childNodes->length==1 and empty($array[$dom->tagName])) {
                            $array[$dom->tagName] = $textContent;
                        } else
                            $array[$dom->tagName]['@value'] = $textContent;
                    }
                }
                else if ($child instanceof \DOMElement) {
                    $nodos_gemelos = $this->countTwins($dom, $child->tagName);
                    if ($nodos_gemelos==1) {
                        if ($arregloNodos)
                            $this->toArray($child, $array);
                        else
                            $this->toArray($child, $array[$dom->tagName]);
                    }
                    // crear arreglo con nodos hijos que tienen el mismo nombre de tag
                    else {
                        if (!isset($array[$dom->tagName][$child->tagName]))
                            $array[$dom->tagName][$child->tagName] = [];
                        $siguiente = count($array[$dom->tagName][$child->tagName]);
                        $array[$dom->tagName][$child->tagName][$siguiente] = [];
                        $this->toArray($child, $array[$dom->tagName][$child->tagName][$siguiente], true);
                    }
                }
            }
        }
        // entregar arreglo
        return $array;
    }

    private function countTwins(\DOMElement $dom, $tagName)
    {
        $twins = 0;
        foreach ($dom->childNodes as $child) {
            if ($child instanceof \DOMElement and $child->tagName==$tagName)
                $twins++;
        }
        return $twins;
    }

    public function getErrors()
    {
        $errors = [];
        foreach (libxml_get_errors() as $e)
            $errors[] = $e->message;
        return $errors;
    }

    public function getName()
    {
        return $this->documentElement->tagName;
    }

    public function getSchema()
    {
        $schemaLocation = $this->documentElement->getAttribute('xsi:schemaLocation');
        if (!$schemaLocation or strpos($schemaLocation, ' ')===false)
            return false;
        list($uri, $xsd) = explode(' ', $schemaLocation);
        return $xsd;
    }

    public function saveXML(\DOMNode $node = null, $options = null)
    {
        $xml = parent::saveXML($node, $options);
        $xml = $this->fixEntities($xml);
        return $xml;
    }

    public function C14N($exclusive = null, $with_comments = null, array $xpath = null, array $ns_prefixes = null)
    {
        $xml = parent::C14N($exclusive, $with_comments, $xpath, $ns_prefixes);
        $xml = $this->fixEntities($xml);
        return $xml;
    }

    private function fixEntities($xml)
    {
        $newXML = '';
        $n_letras = strlen($xml);
        $convertir = false;
        for ($i=0; $i<$n_letras; ++$i) {
            if ($xml[$i]=='>')
                $convertir = true;
            if ($xml[$i]=='<')
                $convertir = false;
            if ($convertir) {
                $l = $xml[$i]=='\'' ? '&apos;' : ($xml[$i]=='"' ? '&quot;' : $xml[$i]);
            } else {
                $l = $xml[$i];
            }
            $newXML .= $l;
        }
        return $newXML;
    }
}
