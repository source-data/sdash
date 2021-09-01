<?php

namespace App\Services;


use \Exception;
use \TCPDF;

class SDPDF extends TCPDF
{

    protected $title = '';
    protected $caption = '';
    protected $imagePath = '';
    protected $imageFormat = '';
    protected $authors = '';

    public function __construct(string $title, string $caption, string $imagePath, string $authors = null, string $imageFormat = null)
    {
        Parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        if (!file_exists($imagePath)) throw new Exception('Attempted to generate a PDF with a non-existent image path');

        $this->title = $title;
        $this->caption = $caption;
        $this->imagePath = $imagePath;
        $this->authors = $authors;
    }

    public function Header()
    {
        $this->setTextColor(27, 97, 209);
        $this->SetFont('helvetica', 'B', '11');
        $this->writeHTML('<div bgcolor="#0e0e53" color="#FFFFFF" font-size="10" style="padding:10px;"><br/>&nbsp; Downloaded from SmartFigures Gallery</div>');
        $this->setTextColor(33, 33, 33);
    }

    public function generateAndReturn()
    {
        // set document information
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('SDash');
        $this->SetTitle($this->title);

        // remove default footer
        $this->setPrintFooter(false);

        // set page margins
        $this->SetMargins(10, PDF_MARGIN_TOP, 10);

        // set auto page breaks
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // add an A4 page
        $this->AddPage('P', 'A4');

        // set JPEG quality
        $this->setJPEGQuality(85);

        $this->SetFont('helvetica', 'B', '16');

        // set start point on page for dumping the output text
        $this->setXY(10, 15);
        $this->MultiCell(0, 0, $this->title, 0, 'L');

        $nextYPosition = $this->getY() + 2;

        // set start point on page for authors
        $this->setXY(10, $nextYPosition);

        $this->SetFont('helvetica', 'I', '12');
        $this->MultiCell(0, 0, $this->authors, 0, 'L');

        $nextYPosition = $this->getY() + 10;

        // set start point on page for writing image
        $this->setXY(10, $nextYPosition);

        if ($this->imageFormat == 'svg') {

            $this->ImageSVG($this->imagePath, null, null, null, null, $link = '', 'C', null, null, $fitonpage = TRUE);
        } else {

            // $this->Image($this->imagePath, null, null, null, null, null, null, 'C', null, null, null, FALSE, FALSE, FALSE, FALSE, FALSE, TRUE, null, null );
            $this->Image(
                $this->imagePath,
                $x = '',
                $y = '',
                $w = 160,
                $h = 240,
                $type = '',
                $link = '',
                $align = '',
                $resize = false,
                $dpi = 300,
                $palign = '',
                $ismask = false,
                $imgmask = false,
                $border = 0,
                $fitbox = 'C M',
                $hidden = false
            );
        }
        // get the image bottom
        $next = $this->getImageRBY();

        $this->setXY(10, $next + 6);
        $this->SetFont('helvetica', 'n', '12');


        // Multicell wraps text
        $this->MultiCell(0, 0, $this->caption, 0, 'L');


        //Close and output PDF document
        $this->Output($this->title . '.pdf', 'D');
    }
}
