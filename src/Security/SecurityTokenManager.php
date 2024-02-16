<?php
namespace App\Security;

use App\Email\SecurityEmail;
use App\Entity\User;
use App\Helper\Code2FAGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityTokenManager 
{
    public function __construct(
            private EntityManagerInterface $em, 
            private TokenGeneratorInterface $tokenGenerator, 
            private SecurityEmail $securityEmail
            )
    {
    }

    public function requireCode2faFrom(User $user)
    {
        $user->setCode2FA(Code2FAGenerator::generate());
        $this->em->persist($user);
        $this->em->flush();
        $this->securityEmail->sendCode2faTo($user);
    }
    public function requireEmailVerificationFrom(User $user)
    {
        $user->setToken($this->tokenGenerator->generateToken());
        $this->em->persist($user);
        $this->em->flush();
        $this->securityEmail->sendConfirmationEmailTo($user, 'security_emailConfirmation');
    }
    public function requirePasswordResetConfirmationFrom(User $user)
    {
        $user->setToken($this->tokenGenerator->generateToken());
        $this->em->persist($user);
        $this->em->flush();
        $this->securityEmail->sendResetPasswordConfirmationEmailTo($user, 'security_newPassword');
    }
    
}