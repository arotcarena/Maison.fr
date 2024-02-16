<?php
namespace App\Helper;

class Code2FAGenerator 
{
    public static function generate(?int $length = 6):string
    {
        return substr(str_shuffle(str_repeat('0123456789', 60)), 0, $length);
    }
}