<?php
namespace App\Email;

use App\Entity\User;
class SecurityEmail extends EmailBuilder
{
    public function sendCode2faTo(User $user):void 
    {
        $this->from(parent::NO_REPLY, parent::NAME)
            ->to($user->getEmail())
            ->subject('Voici votre code d\'authentification')
            ->text($user->getCode2FA())
            ->send()
            ;
    }

    public function sendConfirmationEmailTo(User $user, string $target):void 
    {
        $this->from(parent::NO_REPLY, parent::NAME)
            ->to($user->getEmail())
            ->subject('Bienvenue chez maison.fr !')
            ->html('email/confirmation_email.html.twig', [
                'link' => $this->createLink($user, $target)
            ])
            ->send()
            ;
    }

    public function sendResetPasswordConfirmationEmailTo(User $user, string $target):void 
    {
        $this->from(parent::NO_REPLY, parent::NAME)
            ->to($user->getEmail())
            ->subject('Réinitialisation du mot de passe')
            ->html('email/resetPassword_email.html.twig', [
                'link' => $this->createLink($user, $target)
            ])
            ->send()
            ;
    }

    public function createLink(User $user, string $target)
    {
        return self::HOST . $this->urlGenerator->generate($target, [
            'id' => $user->getId(),
            'token' => $user->getToken()
        ]);
    }

}