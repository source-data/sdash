<?php

namespace App\Services;


use \Exception;

/**
 * ImageData contains information about the image that is passed in the constructor.
 * The utility functions perform calculations on the information to aid in resizing.
 */
class ImageData {

    public $path;
    public $filetype;
    public $layout;
    public $width;
    public $height;
    public $ratio;


    function __construct(string $filepath) {

        if(!file_exists($filepath)) throw new Exception("ImageData utility requires a valid filepath. No file was found.");

        $this->path = $filepath;


        $info = getimagesize($filepath);

        $this->width = $info[0];

        $this->height = $info[1];

        $this->filetype = $info["mime"];

        if( $info[0] > $info[1] ) $this->layout = 'landscape';
        if( $info[0] < $info[1] ) $this->layout = 'portrait';
        if( $info[0] == $info[1] ) $this->layout = 'square';

        $this->ratio = (float)$info[0] / (float)$info[1];


    }


    public function fitToBox(float $targetWidth, float $targetHeight): array {

        $bounds = [];

        if($this->width < $targetWidth && $this->height < $targetHeight) {

            $bounds = ["width" => floor($this->width), "height" => floor($this->height)];

        } else {

            if($this->ratio * $targetHeight > $targetWidth){
                $bounds = ["width" => floor($targetWidth), "height" => floor($targetWidth / $this->ratio)];
            } elseif($targetWidth / $this->ratio > $targetHeight) {
                $bounds = ["width" => floor($targetHeight * $this->ratio), "height" => floor($targetHeight)];
            } else {
                $bounds = ["width" => $targetWidth, "height" => $targetHeight];
            }

        }


        return $bounds;
    }


}