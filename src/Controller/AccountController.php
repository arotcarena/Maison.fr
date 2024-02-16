<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[IsGranted('ROLE_USER')]
class AccountController extends AbstractController
{
    #[Route('/mon-compte', name: 'account_index')]
    public function index():Response
    {
        if($pro = $this->getUser()->getPro())
        {
            return $this->redirectToRoute('pro_show', [
                'id' => $pro->getId()
            ]);
        }
        return $this->render('account/index.html.twig');
    }
}