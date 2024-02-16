<?php 
namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository
    )
    {

    }


    #[Route('/category-suggest/{q}', name: 'category_suggest')]
    function search(string $q = ''): Response
    {
        if($q === '') {
            return new Response(json_encode(([])));
        }
        $categories = $this->categoryRepository->findByQ($q);
        return new Response(json_encode($categories));
    }
}