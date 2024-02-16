<?php
namespace App\Controller;

use App\Entity\Pro;
use App\Entity\City;
use App\Entity\User;
use App\Entity\Category;
use App\DataFixtures\ProFixtures;
use App\Repository\ProRepository;
use App\DataFixtures\CityFixtures;
use App\Repository\CityRepository;
use App\Repository\UserRepository;
use App\DataFixtures\CategoryFixtures;
use App\Repository\CategoryRepository;
use App\DataFixtures\TestUsersFixtures;
use Doctrine\ORM\EntityManagerInterface;
use App\DataFixtures\TestProUserFixtures;
use App\Tests\Controller\AuthenticationTrait;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class ProControllerTest extends WebTestCase
{
    use AuthenticationTrait;

    protected KernelBrowser $client;
    protected UrlGeneratorInterface $urlGenerator;
    protected City $city;
    protected Category $category;
    protected User $user;
    protected EntityManagerInterface $em;
    protected UserRepository $userRepository;
    protected ProRepository $proRepository;

    public function setUp():void 
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->em = $container->get(EntityManagerInterface::class);
        $this->urlGenerator = $container->get(UrlGeneratorInterface::class);
        /** @var AbstractDatabaseTool */
        $dbtool = $container->get(DatabaseToolCollection::class)->get();
        $dbtool->loadFixtures([
            CityFixtures::class,
            CategoryFixtures::class,
            ProFixtures::class, 
            TestUsersFixtures::class,
            TestProUserFixtures::class
        ]);
        $this->city = $container->get(CityRepository::class)->findOneBy([]);
        $this->category = $container->get(CategoryRepository::class)->findOneBy([]);
        $this->user = $container->get(UserRepository::class)->findOneBy([]);
        $this->userRepository = $container->get(UserRepository::class);
        $this->proRepository = $container->get(ProRepository::class);
    }
    public function testIndexResponseSuccessfull()
    {
        $crawler = $this->client->request('GET', $this->urlGenerator->generate('pro_index', [
            'city_slug' => $this->city->getSlug(),
            'category_slug' => $this->category->getSlug()
        ]));
        $this->assertResponseIsSuccessful();
    }
    public function testIndexTitle()
    {
        $crawler = $this->client->request('GET', $this->urlGenerator->generate('pro_index', [
            'city_slug' => $this->city->getSlug(),
            'category_slug' => $this->category->getSlug()
        ]));
        $this->assertSelectorTextContains('title', $this->category->getName().' à '.$this->city->getFullName().' avec maison.fr');
    }
    public function testIndexWrongParametersInUrl()
    {
        $crawler = $this->client->request('GET', $this->urlGenerator->generate('pro_index', [
            'city_slug' => 'nimportequoi',
            'category_slug' => 'encorenimportequoi'
        ]));
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testRemoveDoRemovePro()
    {
        $test_email = $this->getUniqueEmail();
        $this->em->persist($this->createPro()->setEmail($test_email));
        $this->em->flush();

        $pro = $this->getContainer()->get(ProRepository::class)->findOneBy(['email' => $test_email]);

        $this->assertNotNull($pro, 'le pro n\'est pas persisté');

        $this->client->request('GET', $this->urlGenerator->generate('pro_remove', ['id' => $pro->getId()]));

        $pro = $this->getContainer()->get(ProRepository::class)->findOneBy(['email' => $test_email]);
        $this->assertNull($pro, 'le pro n\'est pas correctement supprimé');
    }

    public function testShowResponseSuccessfull()
    {
        $this->client->request('GET', $this->urlGenerator->generate('pro_show', [
            'id' => $this->getPro()->getId()
        ]));
        $this->assertResponseIsSuccessful();
    }

    public function testShowTitle()
    {
        $pro = $this->getPro();
        $this->client->request('GET', $this->urlGenerator->generate('pro_show', [
            'id' => $pro->getId()
        ]));
        $this->assertSelectorTextContains('h1', $pro->getBusinessName());
    }

    public function testShowContainsButtonEditIfUserIsOwner()
    {
        $user = $this->userRepository->findOneBy(['email' => TestProUserFixtures::PRO_USER_1_EMAIL]);
        $pro = $this->proRepository->findOneBy(['email' => TestProUserFixtures::PRO_USER_1_EMAIL]);
        $this->login($user, $this->client);
        $this->client->request('GET', $this->urlGenerator->generate('pro_show', [
            'id' => $pro->getId()
        ]));
        $this->assertSelectorTextContains('body', 'éditer', 'le bouton éditer nest pas présent alors que le user est le propriétaire du compte');
    }
    public function testShowDontContainsButtonEditIfUserIsNotOwner()
    {
        $user = $this->userRepository->findOneBy(['email' => TestProUserFixtures::PRO_USER_1_EMAIL]);
        $pro = $this->proRepository->findOneBy(['email' => TestProUserFixtures::PRO_USER_2_EMAIL]);
        $this->login($user, $this->client);
        $this->client->request('GET', $this->urlGenerator->generate('pro_show', [
            'id' => $pro->getId()
        ]));
        $this->assertSelectorTextNotContains('body', 'éditer', 'le bouton éditer est présent alors que le User nest pas le propriétaire du compte');
    }
    public function testShowDontContainsButtonEditIfAnonymous()
    {
        $pro = $this->proRepository->findOneBy(['email' => TestProUserFixtures::PRO_USER_1_EMAIL]);
        $this->client->request('GET', $this->urlGenerator->generate('pro_show', [
            'id' => $pro->getId()
        ]));
        $this->assertSelectorTextNotContains('body', 'éditer', 'le bouton éditer est présent alors que aucun User connecté');
    }

    public function testEditCanBeAccessedByOwnerUser()
    {
        $user = $this->userRepository->findOneBy(['email' => TestProUserFixtures::PRO_USER_1_EMAIL]);
        $pro = $this->proRepository->findOneBy(['email' => TestProUserFixtures::PRO_USER_1_EMAIL]);
        $this->login($user, $this->client);
        $this->client->request('GET', $this->urlGenerator->generate('pro_edit', [
            'id' => $pro->getId()
        ]));
        $this->assertResponseIsSuccessful('Le propriétaire du compte ne peut pas accéder à la page edit');
    }

    public function testEditCannotBeAccessedByNotOwnerUser()
    {
        $user = $this->userRepository->findOneBy(['email' => TestProUserFixtures::PRO_USER_1_EMAIL]);
        $pro = $this->proRepository->findOneBy(['email' => TestProUserFixtures::PRO_USER_2_EMAIL]);
        $this->login($user, $this->client);
        $this->client->request('GET', $this->urlGenerator->generate('pro_edit', [
            'id' => $pro->getId()
        ]));
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN, 'un User non propriétaire du compte peut accéder à la page edit');
    }

    public function testEditCannotBeAccessedByAnonymous()
    {
        $pro = $this->proRepository->findOneBy(['email' => TestProUserFixtures::PRO_USER_1_EMAIL]);
        $this->client->request('GET', $this->urlGenerator->generate('pro_edit', [
            'id' => $pro->getId()
        ]));
        $this->assertResponseRedirects($this->urlGenerator->generate('security_login'), Response::HTTP_FOUND, 'un anonyme qui essaie de se rendre sur la page edit nest pas redirigé vers login');
    }

    private function getUniqueEmail():string 
    {
        return 'test'. random_int(0, 100000) .'@mail.com';
    }

    private function getPro(?array $criteria = []):Pro
    {
        return self::getContainer()->get(ProRepository::class)->findOneBy($criteria);
    }

    private function getUser(?array $criteria = []):User
    {
        return self::getContainer()->get(UserRepository::class)->findOneBy($criteria);
    }

    private function createPro():Pro
    {
        return (new Pro)
                ->setUser($this->user)
                ->setEmail($this->user->getEmail())
                ->setBusinessName('Entreprise du batiment')
                ->setContactName('Jean Contact')
                ->setCategories(new ArrayCollection([$this->category]))
                ->setCity($this->city)
                ->setPhone('0612131415')
                ->setDepartments('31, 64, 40')
                ;

    }


}