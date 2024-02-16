<?php

namespace App\Controller;

use Exception;
use App\Entity\Pro;
use App\Entity\City;
use App\Entity\Picture;
use App\Entity\Category;
use App\Form\SearchType;
use App\Form\DataModel\Search;
use App\Security\Voter\ProVoter;
use App\Repository\ProRepository;
use App\Repository\CityRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Storage\StorageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProController extends AbstractController
{

    public function __construct(
        private ProRepository $proRepository, 
        private EntityManagerInterface $em, 
        private StorageInterface $storageInterface
        )
    {
    }

    #[Route('/find-pros/category-{category_id}/city-{city_id}/offset-{offset}', name: 'pro_find')]
    #[ParamConverter('city', options: ['mapping' => ['city_id' => 'id']])]
    #[ParamConverter('category', options: ['mapping' => ['category_id' => 'id']])]
    public function find(Category $category, City $city, int $offset = 0): Response
    {
        $pros = $this->proRepository->findByDepartmentAndCategoryHydratedWithFirstPicture($city->getDepartmentCode(), $category, $offset);

        $data = array_map(function(Pro $pro) {
            if($pro->getFirstPicture())
            {
                $imagePath = '/images/pro/'. $this->storageInterface->resolvePath($pro->getFirstPicture(), 'imageFile', Picture::class, true); 
            }
            else
            {
                $imagePath = '/images/logo/maison.png';
            }
            return [
                'businessName' => $pro->getBusinessName(),
                'showCategories' => $pro->showCategories(),
                'firstPicturePath' => $imagePath,
                'showUrl' => $this->generateUrl('pro_show', ['id' => $pro->getId()])
            ];
        }, $pros);
        return new Response(json_encode($data));
    }


    #[Route('{city_slug}/{category_slug}/pros', name: 'pro_index', requirements: ['category_slug' => '[a-z\-]+', 'city_slug' => '[a-z\-]+'])]
    #[ParamConverter('city', options: ['mapping' => ['city_slug' => 'slug']])]
    #[ParamConverter('category', options: ['mapping' => ['category_slug' => 'slug']])]
    public function index(City $city, Category $category, Request $request): Response
    {
        $search = (new Search)
                    ->setCategory($category)
                    ->setCity($city)
                    ;
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) 
        { 
            return $this->redirectToRoute('pro_index', [
                'city_slug' => $search->getCity()->getSlug(), 
                'category_slug' => $search->getCategory()->getSlug()
            ]);
        }
        
        $pros = $this->proRepository->findByDepartmentAndCategoryHydratedWithFirstPicture($city->getDepartmentCode(), $category);
        $count = $this->proRepository->count([]);
        return $this->render('pro/index.html.twig', [
            'city' => $city,
            'category' => $category,
            'form' => $form->createView(), 
            'pros' => $pros,
            'count' => $count
        ]);
    }

    #[Route('/pro/{id}', name: 'pro_show', requirements: ['id' => '\d+'])]
    public function show(Pro $pro): Response 
    {
        return $this->render('pro/show.html.twig', [
            'pro' => $pro
        ]);
    }

    #[Route('/pro/edit/{id}', name: 'pro_edit', requirements: ['id' => '\d+'])]
    public function edit(Pro $pro): Response 
    {
        $this->denyAccessUnlessGranted('CAN_EDIT', $pro, 'Vous ne pouvez pas éditer ce pro car vous nen etes pas le propriétaire');
        return $this->render('pro/edit.html.twig', [
            'pro' => $pro
        ]);
    }

    #[Route('pro-remove/{id}', name: 'pro_remove')]
    public function remove(Pro $pro, Request $request): Response
    {
        $businessName = $pro->getBusinessName();
        $this->em->remove($pro);
        $this->em->flush();
        $this->addFlash('success', 'le pro "'.$businessName.'" a bien été supprimé !');
        return $this->redirect($request->get('target', 'home'));
        
    }
}
