<?php

namespace App\Services;


use \Exception;
use App\Services\ImageData;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Shape\Drawing;
use PhpOffice\PhpPresentation\Shape\Drawing\Base64;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\RichText\BreakElement;
use PhpOffice\PhpPresentation\Shape\RichText\TextElement;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Log;

class SDPowerpoint
{

    protected $title = '';
    protected $caption = '';
    protected $imagePath = '';
    protected $imageFormat = '';
    protected $authors = '';
    protected $url = null;
    protected $document = null;


    public function __construct(string $title, string $caption, string $imagePath, string $url = null, string $authors = null, string $imageFormat = null)
    {

        if (!file_exists($imagePath)) throw new Exception('Attempted to export from a non-existent image path', 500);

        $this->title = $title;
        $this->caption = $caption;
        $this->imagePath = $imagePath;
        if (isset($url)) $this->url = $url;
        if (isset($authors)) $this->authors = $authors;
        $this->imageFormat = $imageFormat;
        $this->document = new PhpPresentation();
    }

    public function generateAndReturn()
    {

        $imageInfo = new ImageData($this->imagePath);
        $dimensions = $imageInfo->fitToBox(600, 400); //width, height

        // Create slide
        $currentSlide = $this->document->getActiveSlide();

        // Create a shape (text) for the title
        $oShapeRichText = new RichText();
        $oShapeRichText->setWidth(800)
            ->setHeight(40)
            ->setOffsetX(40)
            ->setOffsetY(40);
        $oShapeRichText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $textRun = $oShapeRichText->createTextRun($this->title);
        $textRun->getFont()->setBold(true)
            ->setSize(16);
        $currentSlide->addShape($oShapeRichText);

        // Create a shape (text) for the authors
        $oShapeRichText = new RichText();
        $oShapeRichText->setWidth(800)
            ->setHeight(40)
            ->setOffsetX(40)
            ->setOffsetY(85);
        $oShapeRichText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $textRun = $oShapeRichText->createTextRun($this->authors);
        $textRun->getFont()->setBold(false)->setItalic(true)
            ->setSize(14);
        $currentSlide->addShape($oShapeRichText);


        // Add a file drawing to the slide
        $shape = new Drawing\File();
        $shape->setName('Figure')
            ->setDescription('Figure')
            ->setPath($this->imagePath)
            ->setOffsetX(40)
            ->setOffsetY(130)
            ->setHeight($dimensions["height"])
            ->setWidth($dimensions["width"]);


        $currentSlide->addShape($shape);


        // Create a shape (text)
        $oShapeRichText = new RichText();
        $oShapeRichText->setWidth(800)
            ->setOffsetX(40)
            ->setOffsetY(560);
        $oShapeRichText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $textRun = $oShapeRichText->createTextRun($this->caption);
        $textRun->getFont()->setBold(false)
            ->setSize(12);
        $currentSlide->addShape($oShapeRichText);


        // Add the QR code if a URL was supplied
        /*
        if(isset($this->url)){

            $QRrender = new ImageRenderer(
                new RendererStyle(150, 5),
                new ImagickImageBackEnd('jpeg')
            );

            $QRwriter = new Writer($QRrender);

            $QRcode = base64_encode($QRwriter->writeString($this->url));

            $QRimage = new Base64();

            $QRimage->setName("Link to this slide")
                    ->setDescription($this->title)
                    ->setData('data:image/jpeg;base64,' . $QRcode)
                    ->setResizeProportional(false)
                    ->setWidth(80)
                    ->setHeight(80)
                    ->setOffsetX(840)
                    ->setOffsetY(30);

            $currentSlide->addShape($QRimage);

        }
        */


        $oWriter = IOFactory::createWriter($this->document, 'PowerPoint2007');

        header("Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation");
        header("Content-Disposition: attachment; filename=\"{$this->title}.pptx\"");

        $oWriter->save('php://output');
    }
}
