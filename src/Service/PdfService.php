<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private $domPdf;
    
    public function __construct()
    {
        $this->domPdf = new Dompdf();
        
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        
        $this->domPdf->setOptions($pdfOptions);
    }
    
    public function generatePdf($html)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->setPaper('A4', 'portrait');
        $this->domPdf->render();
        
        return $this->domPdf->output();
    }
    
    public function generateBinaryPDF($html)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->setPaper('A4', 'portrait');
        $this->domPdf->render();
        
        return $this->domPdf->output();
    }
    
    public function streamPdf($html, $filename)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->setPaper('A4', 'portrait');
        $this->domPdf->render();
        
        $this->domPdf->stream($filename, [
            'Attachment' => true
        ]);
        
        exit(0);
    }
} 