<?php

namespace App\Tests\Controller;

use App\Entity\City;
use App\Entity\Category;
use App\DataFixtures\CityFixtures;
use App\Repository\CityRepository;
use App\DataFixtures\CategoryFixtures;
use App\Repository\CategoryRepository;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use App\DataFixtures\TestFixtures\CityTestFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\TestFixtures\CategoryTestFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class HomeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Crawler $crawler;

    public function setUp():void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->crawler = $this->client->request('GET', $this->client->getContainer()->get(UrlGeneratorInterface::class)->generate('home'));
    }
    public function testResponseIsSuccessfull(): void
    {
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseIsSuccessful();
    }

    public function testPageContent():void 
    {
        $this->assertSelectorExists('nav');
        $this->assertSelectorTextContains('h1', 'Prenez RDV avec un pro de l\'Habitat');
    }

    // public function testSearch():void 
    // {
    //     $container = self::getContainer();
    //     /** @var AbstractDatabaseTool */
    //     $dbtool = $container->get(DatabaseToolCollection::class)->get();
    //     $dbtool->loadFixtures([
    //         CategoryFixtures::class,
    //         CityFixtures::class
    //     ]);

    //     $categories = $container->get(CategoryRepository::class)->findAll();
    //     $cities = $container->get(CityRepository::class)->findAll();
    //     /** @var Category */
    //     $category = $categories[random_int(0, count($categories) - 1)];
    //     /** @var City */
    //     $city = $cities[random_int(0, count($cities) - 1)];
        
    //     $form = $this->crawler->selectButton('Rechercher')->form([
    //         'category' => $category->getId(),
    //         'city' => $city->getId()
    //     ]);
    //     $this->client->submit($form);
    //     $this->assertResponseRedirects($this->client->getContainer()->get(UrlGeneratorInterface::class)->generate('pro_index', [
    //         'city_slug' => $city->getSlug(),
    //         'category_slug' => $category->getSlug()
    //     ]));
    // }
}
