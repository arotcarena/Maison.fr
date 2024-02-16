<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\DataModel\Register;
use App\Form\ForgottenPasswordType;
use App\Form\LoginType;
use App\Form\NewPasswordType;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use App\Security\SecurityTokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
       private UserRepository $userRepository,
       private EntityManagerInterface $em,
       private SecurityTokenManager $securityTokenManager
    )
    {
    }

    #[Route(path: '/login', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        $form = $this->createForm(LoginType::class, null, [
            'view2FA' => $error && $error->getCode() === AppAuthenticator::APP_2FA_ERROR,
            'lastUsername' => $request->getSession()->get(AppAuthenticator::LAST_USERNAME),
            'lastPassword' => $request->getSession()->get(AppAuthenticator::LAST_PASSWORD),
            'lastCode2FA' => $request->getSession()->get(AppAuthenticator::LAST_CODE_2FA),
            'lastRememberMe' => $request->getSession()->get(AppAuthenticator::LAST_REMEMBER_ME)
        ]);

        //traduction du message dans le cas d'une erreur d'idenfifiants
        if($error && !in_array($error->getCode(), [AppAuthenticator::APP_2FA_ERROR, AppAuthenticator::APP_CONFIRMED_ERROR]))
        {
            $error = new AuthenticationException('Identifiants invalides');
        }

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
            'view2FA' => $error && $error->getCode() === AppAuthenticator::APP_2FA_ERROR,
            'viewNotConfirmed' => $error && $error->getCode() === AppAuthenticator::APP_CONFIRMED_ERROR
        ]);
    }

    #[Route(path: '/logout', name: 'security_logout')]
    public function logout(): void
    {
    }

    #[Route('/confirmation-de-l-email', name: 'security_emailConfirmation')]
    public function emailConfirmation(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->verifyToken($request);
        $user->setConfirmed(true)
                ->setToken(null);
        $em->flush();
        $this->addFlash('success', 'Votre adresse email a été confirmée !');
        return $this->redirectToRoute('security_login');
    }

    #[Route('/création-d-un-nouveau-mot-de-passe', name: 'security_newPassword')]
    public function newPassword(Request $request, UserPasswordHasherInterface $hasher)
    {
        $user = $this->verifyToken($request);
        $register = new Register;
        $form = $this->createForm(NewPasswordType::class, $register);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) 
        { 
            $user->setPassword($hasher->hashPassword($user, $register->getPassword()))
                ->setToken(null)
                ;
            $this->em->flush();
            $this->addFlash('success', 'le mot de passe a bien été changé');
            return $this->redirectToRoute('security_login');
        }
        return $this->render('security/newPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/mot-de-passe-oublie', name: 'security_forgottenPassword')]
    public function forgottenPassword(Request $request):Response
    {
        $form = $this->createForm(ForgottenPasswordType::class);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) 
        { 
            $email = $form->getData()['email'];
            $user = $this->userRepository->findOneBy(['email' => $email]);
            if(!$user)
            {
                throw new UserNotFoundException();
            }
            $this->securityTokenManager->requirePasswordResetConfirmationFrom($user);
            $this->addFlash('success', 'un lien pour réinitialiser votre mot de passe vous a été envoyé par email');
        }
        return $this->render('security/forgottenPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/new-code-2fa', name: 'security_newCode2fa')]
    public function newCode2fa(Request $request): Response
    {
        $user = $this->userRepository->findOneBy(['email' => $request->getSession()->get(AppAuthenticator::LAST_USERNAME)]);
        if($user)
        {
            $this->securityTokenManager->requireCode2faFrom($user);
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, new AuthenticationException('Entrez le nouveau code envoyé par email', AppAuthenticator::APP_2FA_ERROR));
        }
        return $this->redirectToRoute('security_login');
    }

    #[Route('/new-confirmation-email', name: 'security_newConfirmationEmail')]
    public function newConfirmationEmail(Request $request): Response
    {
        $user = $this->userRepository->findOneBy(['email' => $request->getSession()->get(AppAuthenticator::LAST_USERNAME)]);
        if($user)
        {
            $this->securityTokenManager->requireEmailVerificationFrom($user);
            $this->addFlash('success', 'Un nouvel email de confirmation vous a été envoyé. Merci de cliquer sur le lien présent dans cet email');
        }
        return $this->redirectToRoute('security_login');
    }

    private function verifyToken(Request $request):User
    {
        /** @var User */
        $user = $this->userRepository->find($request->get('id'));
        if($user === null || $user->getToken() !== $request->get('token'))
        {
            throw new NotFoundHttpException('Le lien utilisé n\'est pas valide');
        }
        return $user;
    }
}
