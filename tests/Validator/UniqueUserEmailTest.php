<?php
namespace App\Tests\Validator;

use PHPUnit\Framework\TestCase;
use App\Validator\UniqueUserEmail;

class UniqueUserEmailTest extends TestCase
{
    public function testOptionIsSetAsProperty()
    {
        $constraint = new UniqueUserEmail([
            'message' => 'test'
        ]);
        $this->assertSame('test', $constraint->message);
    }
}
