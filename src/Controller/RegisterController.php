<?php
namespace App\Controller;

use App\Form\RegisterType;
use App\Email\SecurityEmail;
use App\Email\ConfirmationEmail;
use App\Form\DataModel\Register;
use App\Persister\RegisterPersister;
use App\Security\SecurityTokenManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'register_index')]
    public function index(Request $request, RegisterPersister $registerPersister, SecurityTokenManager $securityTokenManager): Response
    {
        $register = new Register;
        $form = $this->createForm(RegisterType::class, $register);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) 
        { 
           $user = $registerPersister->persist($register);
        //    $securityTokenManager->requireEmailVerificationFrom($user);
           $this->addFlash('success', 'l\'inscription s\'est bien passée, veuillez à présent cliquer sur le lien présent dans l\'email de bienvenue que nous vous avons envoyé afin de confirmer votre compte');
           return $this->redirectToRoute('account_index');
        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}