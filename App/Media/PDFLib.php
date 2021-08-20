<?php

namespace App\Media;

use Dompdf\Dompdf;

class PDFLib
{

    private Dompdf $pdf;

    public function __construct()
    {
        $this->pdf = new Dompdf();
    }

    public function generatePdf($content, $name, $paper = "A3", $display = "landscape")
    {
        // chmod($content, 2);
        $this->pdf->loadHtml($content, "UTF-8");
        $this->pdf->setPaper($paper, $display);
        $this->pdf->render();
        $output = $this->pdf->output();
        file_put_contents($name, $output);
    }
}
