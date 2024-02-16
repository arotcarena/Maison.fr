<?php
namespace App\Tests\Form\DataModel;

use App\Entity\Category;
use App\Entity\City;
use App\Form\DataModel\Search;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TypeError;

class SearchTest extends KernelTestCase
{
    protected ValidatorInterface $validator;

    public function setUp():void 
    {
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    protected function createSearch(): Search
    {
        return (new Search)
                ->setCategory(new Category)
                ->setCity(new City)
                ;
    }
    protected function assertHasErrors(int $expectedErrors, Search $search)
    {
         /** @var ConstraintViolation[] */
         $errors = $this->validator->validate($search);
         $messages = [];
         foreach($errors as $error)
         {
             $messages[] = $error->getMessage();
         }
         $this->assertCount($expectedErrors, $errors, implode(', ', $messages));
    }
    public function testSearchWithNullCity()
    {
        $search = $this->createSearch()
                        ->setCity(null)
                        ;
        $this->assertHasErrors(1, $search);
    }
    public function testSearchWithStringCity()
    {
        $this->expectException(TypeError::class);
        $search = $this->createSearch()
                        ->setCity('ville')
                        ;
    }
    public function testSearchWithStringCategory()
    {
        $this->expectException(TypeError::class);
        $search = $this->createSearch()
                        ->setCategory('catégorie')
                        ;
    }
    public function testSearchWithNullCategory()
    {
        $search = $this->createSearch()
                        ->setCategory(null)
                        ;
        $this->assertHasErrors(1, $search);
    }
    public function testSearchWithCorrectsAttributes()
    {
        $this->assertHasErrors(0, $this->createSearch());
    }
}