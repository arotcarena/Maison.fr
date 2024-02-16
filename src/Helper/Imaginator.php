<?php
namespace App\Helper;

class Imaginator
{
    public function createJpegImage(string $relativePath)
    {
        $image = imagecreate(200,50);
        imagejpeg($image, $relativePath);
    }
}