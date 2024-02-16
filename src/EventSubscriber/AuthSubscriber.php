<?php

namespace App\EventSubscriber;

use App\Email\Code2FAEmail;
use App\Entity\User;
use App\Helper\Code2FAGenerator;
use App\Security\AppAuthenticator;
use App\Security\SecurityTokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class AuthSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $request, 
        private SecurityTokenManager $securityTokenManager,
        private EntityManagerInterface $em,
        private Security $security
        )
    {
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        /** @var User */
        $user = $event->getAuthenticationToken()->getUser();
        if($user === null || !$user->isConfirmed())
        {
            throw new AuthenticationException('Vous n\'avez pas confirmé votre adresse email', AppAuthenticator::APP_CONFIRMED_ERROR);
        }
        if($user->isChoice2FA())
        {
            $code2FA = $this->request->getSession()->get(AppAuthenticator::CODE_2FA);
            $this->request->getSession()->set(AppAuthenticator::CODE_2FA, null);
            if($code2FA === null)
            {
                $this->securityTokenManager->requireCode2faFrom($user);
                throw new AuthenticationException('Veuillez entrer le code qui vous a été envoyé par email', AppAuthenticator::APP_2FA_ERROR);
            }
            elseif($code2FA !== $user->getCode2FA())
            {
                throw new AuthenticationException('Le code est incorrect', AppAuthenticator::APP_2FA_ERROR);
            }
        }
    }

    public function onLoginSuccess(LoginSuccessEvent $event)
    {
        if($this->security->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $event->getRequest()->getSession()->getBag('flashes')->add('success', 'Vous êtes connecté !');
        }
    }

    public function onLogout(LogoutEvent $event)
    {
        $event->getRequest()->getSession()->getBag('flashes')->add('success', 'Vous êtes déconnecté !');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationSuccessEvent::class => 'onAuthenticationSuccess',
            LoginSuccessEvent::class => 'onLoginSuccess',
            LogoutEvent::class => 'onLogout'
        ];
    }
}
