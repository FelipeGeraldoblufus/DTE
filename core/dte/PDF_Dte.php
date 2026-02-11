<?php
/**
 * Clase para generar PDFs
 */
class PDF_Dte extends TCPDF
{

    private $footer; ///< Mensaje a colocar en el footer
    private $hbanner;
    private $fbanner;
    private $dbanner;

    protected $defaultOptions = [
        'font' => ['family' => 'helvetica', 'size' => 9],
        'table' => [
            'fontsize' => 9,
            'width' => 186,
            'height' => 6,
            'align' => 'C',
            'headerbackground' => [255, 255, 255],
            'headercolor' => [0, 0, 0],
            'bodybackground' => [255, 255, 255],
            'bodycolor' => [0, 0, 0],
            'colorchange' => false,
        ],
    ];

    public function __construct($o = 'P', $u = 'mm', $s = 'LETTER', $top = 0)
    {
        parent::__construct($o, $u, $s);
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP+$top, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER+$top);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER+6);
        $this->setFont($this->defaultOptions['font']['family']);
    }

    public function Header($x = 0, $y = 0, $w_img = 216)
    {
        // banner del documento
        $image_file = $this->getHeaderBanner();
        $this->Image($image_file, $x, $y, $w_img, 'PNG');
        $this->Banner();
    }

    public function setHeaderBanner($banner)
    {
        $this->hbanner = $banner;
    }

    public function getHeaderBanner()
    {
        return $this->hbanner;
    }

    public function Footer($x = 10, $y = 245, $w_img = 190)
    {
        // banner del documento
        $image_file = $this->getFooterBanner();
        $this->Image($image_file, $x, $y, $w_img, 'PNG');
    }

    public function setFooterBanner($banner)
    {
        $this->fbanner = $banner;
    }

    public function getFooterBanner()
    {
        return $this->fbanner;
    }

    public function Banner($x = 145, $y = 81, $w_img = 55)
    {
        // banner del documento
        $image_file = $this->getBanner();
        $this->Image($image_file, $x, $y, $w_img, 'PNG');
    }

    public function setBanner($banner)
    {
        $this->dbanner = $banner;
    }

    public function getBanner()
    {
        return $this->dbanner;
    }


    private function getTableCellWidth($total, $cells)
    {
        $widths = [];
        if (is_int($cells)) {
            $width = floor($total/$cells);
            for ($i=0; $i<$cells; ++$i) {
                $widths[] = $width;
            }
        }
        else if (is_array($cells)){
            $width = floor($total/count($cells));
            foreach ($cells as $i) {
                $widths[$i] = $width;
            }
        }
        return $widths;
    }

    public function addTableWithoutEmptyCols($titles, $data, $options = [], $html = true)
    {
        $cols_empty = [];
        foreach ($data as $row) {
            foreach ($row as $col => $value) {
                if (empty($value)) {
                    if (!array_key_exists($col, $cols_empty))
                        $cols_empty[$col] = 0;
                    $cols_empty[$col]++;
                }
            }
        }
        $n_rows = count($data);
        $titles_keys = array_flip(array_keys($titles));
        foreach ($cols_empty as $col => $rows) {
            if ($rows==$n_rows) {
                unset($titles[$col]);
                foreach ($data as &$row) {
                    unset($row[$col]);
                }
                if (isset($options['width']))
                    unset($options['width'][$titles_keys[$col]]);
                if (isset($options['align']))
                    unset($options['align'][$titles_keys[$col]]);
            }
        }
        if (isset($options['width'])) {
            $options['width'] = array_slice($options['width'], 0);
            $key_0 = null;
            $suma = 0;
            foreach ($options['width'] as $key => $val) {
                if ($val===0)
                    $key_0 = $key;
                $suma += $val;
            }
            if ($key_0!==null) {
                $options['width'][$key_0] = 130 - $suma;
            }
        }
        if (isset($options['align']))
            $options['align'] = array_slice($options['align'], 0);
        $this->addTable($titles, $data, $options, $html);
    }

    public function addTable($headers, $data, $options = [], $html = true)
    {
        $options = array_merge($this->defaultOptions['table'], $options);
        if ($html) {
            $this->addHTMLTable($headers, $data, $options);
        } else {
            $this->addNormalTable($headers, $data, $options);
        }
    }

    private function addHTMLTable($headers, $data, $options = [])
    {
        $w = (isset($options['width']) and is_array($options['width'])) ? $options['width'] : null;
        $a = (isset($options['align']) and is_array($options['align'])) ? $options['align'] : [];
        $buffer = '<table style="border:1px solid #333">';
        // Definir títulos de columnas
        $thead = isset($options['width']) and is_array($options['width']) and count($options['width']) == count($headers);
        if ($thead)
            $buffer .= '<thead>';
        $buffer .= '<tr>';
        $i = 0;
        foreach ($headers as &$col) {
            $width = ($w and isset($w[$i])) ? (';width:'.$w[$i].'mm') : '';
            $align = isset($a[$i]) ? $a[$i] : 'center';
            $buffer .= '<th style="border-right:1px solid #333;border-bottom:1px solid #333;text-align:'.$align.$width.'"><strong>'.strip_tags($col).'</strong></th>';
            $i++;
        }
        $buffer .= '</tr>';
        if ($thead)
            $buffer .= '</thead>';
        // Definir datos de la tabla
        if ($thead)
            $buffer .= '<tbody>';
        foreach ($data as &$row) {
            $buffer .= '<tr>';
            $i = 0;
            foreach ($row as &$col) {
                $width = ($w and isset($w[$i])) ? (';width:'.$w[$i].'mm') : '';
                $align = isset($a[$i]) ? $a[$i] : 'center';
                $buffer .= '<td style="border-right:1px solid #333;text-align:'.$align.$width.'">'.$col.'</td>';
                $i++;
            }
            $buffer .= '</tr>';
        }
        if ($thead)
            $buffer .= '</tbody>';
        // Finalizar tabla
        $buffer .= '</table>';
        // generar tabla en HTML
        $this->writeHTML($buffer, true, false, false, false, '');
    }

    private function addNormalTable($headers, $data, $options = array())
    {
        // Colors, line width and bold font
        $this->SetFillColor(
            $options['headerbackground'][0],
            $options['headerbackground'][1],
            $options['headerbackground'][2]
        );
        $this->SetTextColor(
            $options['headercolor'][0],
            $options['headercolor'][1],
            $options['headercolor'][2]
        );
        $this->SetFont($this->defaultOptions['font']['family'], 'B',  $options['fontsize']);
        // Header
        $w = is_array($options['width']) ? $options['width'] :
            $this->getTableCellWidth($options['width'], array_keys($headers));
        foreach($headers as $i => $header) {
            $this->Cell ($w[$i], $options['height'], $headers[$i], 1, 0, $options['align'], 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor (
            $options['bodybackground'][0],
            $options['bodybackground'][1],
            $options['bodybackground'][2]
        );
        $this->SetTextColor(
            $options['bodycolor'][0],
            $options['bodycolor'][1],
            $options['bodycolor'][2]
        );
        $this->SetFont($this->defaultOptions['font']['family']);
        // Data
        $fill = false;
        foreach ($data as &$row) {
            $num_pages = $this->getNumPages();
            $this->startTransaction();
            foreach($headers as $i => $header) {
                $this->Cell ($w[$i], $options['height'], $row[$i], 'LR', 0, $options['align'], $fill);
            }
            $this->Ln();
            if($num_pages < $this->getNumPages()) {
                $this->rollbackTransaction(true);
                $this->AddPage();
                foreach($headers as $i => $header) {
                    $this->Cell ($w[$i], $options['height'], $headers[$i], 1, 0, $options['align'], 1);
                }
                $this->Ln();
                foreach($headers as $i => $header) {
                    $this->Cell ($w[$i], $options['height'], $row[$i], 'LR', 0, $options['align'], $fill);
                }
                $this->Ln();
            } else {
                $this->commitTransaction();
            }
            if ($options['colorchange'])
                $fill = !$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln();
    }

    public function Texto($txt, $x=null, $y=null, $align='', $w=0, $link='', $border=0, $fill=false)
    {
        if ($x==null) $x = $this->GetX();
        if ($y==null) $y = $this->GetY();
        $textrendermode = $this->textrendermode;
        $textstrokewidth = $this->textstrokewidth;
        $this->setTextRenderingMode(0, true, false);
        $this->SetXY($x, $y);
        $this->Cell($w, 0, $txt, $border, 0, $align, $fill, $link);
        // restore previous rendering mode
        $this->textrendermode = $textrendermode;
        $this->textstrokewidth = $textstrokewidth;
    }

    public function MultiTexto($txt, $x=null, $y=null, $align='', $w=0, $border=0, $fill=false)
    {
        if ($x==null) $x = $this->GetX();
        if ($y==null) $y = $this->GetY();
        $textrendermode = $this->textrendermode;
        $textstrokewidth = $this->textstrokewidth;
        $this->setTextRenderingMode(0, true, false);
        $this->SetXY($x, $y);
        $this->MultiCell($w, 0, $txt, $border, $align, $fill);
        // restore previous rendering mode
        $this->textrendermode = $textrendermode;
        $this->textstrokewidth = $textstrokewidth;
    }

}