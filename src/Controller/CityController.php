<?php 
namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CityController extends AbstractController
{
    public function __construct(
        private CityRepository $cityRepository
    )
    {

    }


    #[Route('/city-suggest/{q}', name: 'city_suggest')]
    function search(string $q = ''): Response
    {
        if($q === '') {
            return new Response(json_encode(([])));
        }
        $cities = $this->cityRepository->findByQ($q);
        return new Response(json_encode($cities));
    }
}