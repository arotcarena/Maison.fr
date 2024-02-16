<?php
namespace App\Helper;

use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class PasswordHasherFactory
{
    private PasswordHasherInterface $hasher;

    public function __construct(PasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function getPasswordHasher():PasswordHasherInterface
    {
        return $this->hasher;
    }
}