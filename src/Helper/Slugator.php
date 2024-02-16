<?php
namespace App\Helper;

class Slugator
{
    public static function slugify(string $text):string
    {
        $slug = strtolower(str_replace(' ', '-', $text));
        $slug = str_replace('ç', 'c', $slug);
        $slug = str_replace(['é', 'è', 'ê', 'ë'], 'e', $slug);
        $slug = str_replace(['â', 'à'], 'a', $slug);
        $slug = str_replace(['ù', 'û'], 'u', $slug);
        return $slug;
    }
}