<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class MakePDF
{
    protected $dompdf;
    protected $filename;
    protected $content;
    protected $header;

    public function __construct()
    {
        // Initialize dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true); // Enable PHP for calculations if needed
        $this->dompdf = new Dompdf($options);
    }

    public function setFileName($filename)
    {
        $this->filename = $filename;
    }

    public function setContent($html)
    {
        $this->content = $html;
    }

    public function setHeader($header)
    {
        $this->header = $header;
    }

    // Get the header HTML (you can call this method to generate the header content)
    private function getHeaderReport()
    {
        $image1 = base_url() . 'assets/images/mgikk-logo.png';

        // Header HTML with logo and title
        $header = '<table width="100%" style="border-bottom:1px solid #000;">
            <tr>
                <td width="50%" align="left"><h3>GPS Based Tracking - Reports</h3></td>
                <td width="50%" align="right"><img src="' . $image1 . '" height="30"/></td>
            </tr>
        </table>';

        return $header;
    }
      

    // Get the footer HTML (you can call this method to generate the footer content)
    private function getFooterReport($canvas)
    {
        // Footer HTML with page numbers and custom text
        $footer = '<div style="text-align:center; font-size:10px; padding-top:10px;">
            Page: {PAGE_NUM} of {PAGE_COUNT}
        </div>';

        // Render footer at the bottom of every page
        $canvas->page_text(270, 770, $footer, null, 10, array(0, 0, 0));

        return $footer;
    }
    // Add header and footer to the PDF content
    public function getPdf($stream = true)
    {
        // Combine the header with the content
        $header = $this->getHeaderReport(); // or use $this->header if you set it from elsewhere
        

        $footer = "<table width='100%' style='border-top:1px solid #000;font-style:italic;font-size:10px;'>
            <tr>
                <td>System Report Generated on ". date('Y-m-d H:i:s') ."</td>
            </tr>
        </table>";

        // $htmlContent = $header . $this->content .  $footer; // Combine header and main content
        $htmlContent = $this->content .  $footer;

        // Load HTML content into dompdf
        $this->dompdf->loadHtml($htmlContent);

        // (Optional) Set paper size
        $this->dompdf->setPaper('A4', 'landscape');

        // Render PDF (first pass)
        $this->dompdf->render();

        // Add the dynamic footer content
        $canvas = $this->dompdf->getCanvas();
        $canvas->page_text(270, 770, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 12, array(0,0,0)); // Change the position as needed

        // Stream the PDF or save to a file
        if ($stream) {
            // Stream to browser
            $this->dompdf->stream($this->filename, ['Attachment' => 0]);
        } else {
            // Save PDF to a file
            file_put_contents('/var/www/html/ncrjhs/writable/reports/' . $this->filename, $this->dompdf->output());
        }
    }
}
