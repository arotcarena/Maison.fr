<?php
namespace App\Tests\Helper;

use App\Helper\Code2FAGenerator;
use PHPUnit\Framework\TestCase;

class Code2FAGeneratorTest extends TestCase
{
    public function testCodeLength()
    {
        $this->assertEquals(15, strlen(Code2FAGenerator::generate(15)), 'le code généré ne fait pas le bon nombre de caractères');
    }
    public function testCodeType()
    {
        $this->assertIsString(Code2FAGenerator::generate(), 'le code généré n\'est pas une chaine de caractères');
    }
    public function testCodeIsDifferentEachTime()
    {
        $this->assertNotSame(Code2FAGenerator::generate(), Code2FAGenerator::generate(), 'le code généré est le même à chaque fois');
    }
}