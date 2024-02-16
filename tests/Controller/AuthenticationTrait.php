<?php
namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\DataFixtures\TestUserFixtures;
use App\DataFixtures\TestUsersFixtures;
use Symfony\Component\BrowserKit\Cookie;
use App\DataFixtures\TestUserConfirmedFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use App\DataFixtures\TestUserConfirmedWith2FAFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Component\HttpFoundation\Session\SessionFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait AuthenticationTrait
{
    public function login(User $user, KernelBrowser $client)
    {
        $client->request('GET', '/');
        $session = $client->getRequest()->getSession();
        $session->set('_security_main', serialize(new UsernamePasswordToken($user, 'main', $user->getRoles())));
        $session->save();


        // OU COMME CA

        // /** @var SessionFactory */
        // $sessionFactory = $client->getContainer()->get('session.factory');
        // /** @var SessionInterface */
        // $session = $sessionFactory->createSession();
        // $session->set('_security_main', serialize(new UsernamePasswordToken($user, 'main', $user->getRoles())));
        // $session->save();
        // $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
    }

    public function isLogged(KernelBrowser $client):bool
    {
        $client->request('GET', '/');
        return $client->getRequest()->getSession()->get('_security_main') !== null ? true: false;
    }

    public function getTestUser(ContainerInterface $container):User
    {
        return $container->get(UserRepository::class)->findAll()[0];
    }

    public function loadTestUsersFixtures(ContainerInterface $container):void 
    {
        /** @var AbstractDatabaseTool */
        $dbtool = $container->get(DatabaseToolCollection::class)->get();
        $dbtool->loadFixtures([TestUsersFixtures::class]);
    }
}