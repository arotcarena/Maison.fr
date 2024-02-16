<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Helper\Slugator;
use App\Form\DataModel\Search;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\UserRepository;
use App\Validator\HomeFormValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HomeController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private CityRepository $cityRepository,
        private HomeFormValidator $homeFormValidator
    )
    {

    }

    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        $search = new Search;
        $this->homeFormValidator->handleRequest($request, $search);

        if($this->homeFormValidator->isSubmitted() && $this->homeFormValidator->isValid())
        {
            return $this->redirectToRoute('pro_index', [
                    'city_slug' => $search->getCity()->getSlug(), 
                    'category_slug' => $search->getCategory()->getSlug()
            ]);
        }
        
        return $this->render('home/index.html.twig', [
            'errors' => $this->homeFormValidator->getErrors()
        ]);
    }
    
}
