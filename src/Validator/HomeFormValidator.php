<?php
namespace App\Validator;

use Exception;
use App\Form\DataModel\Search;
use App\Repository\CityRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class HomeFormValidator
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    private $errors = [];

    private bool $submitted = false;

    public function __construct(
        private ValidatorInterface $validator,
        private CityRepository $cityRepository,
        private CategoryRepository $categoryRepository,
        private CsrfTokenManagerInterface $tokenManager
        
    )
    {}

    public function handleRequest(Request $request, Search $search)
    {
        if($request->isMethod('POST'))
        { 
            $this->submitted = true;

            if(!$this->tokenManager->isTokenValid(new CsrfToken('home-form', $request->get('_token'))))
            {
                throw new Exception('le token csrf n\'est pas valide');
            }
            
            $category = $this->categoryRepository->find((int)$request->get('category'));
            $city = $this->cityRepository->find((int)$request->get('city'));

            $search->setCategory($category);
            $search->setCity($city);

            /** @var ConstraintViolationList */
            $errors = $this->validator->validate($search);
            foreach($errors as $error)
            {
                $this->errors[$error->getPropertyPath()] = $error->getMessage();
            }
        }
    }

    public function isSubmitted(): bool 
    {
        return $this->submitted;
    }

    public function isValid():bool 
    {
        return empty($this->errors);
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getErrors() 
    {
        return $this->errors;
    }


}