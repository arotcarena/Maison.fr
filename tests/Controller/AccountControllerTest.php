<?php
namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Controller\LoginUserToClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\SessionFactory;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionFactoryInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AccountControllerTest extends WebTestCase
{
    use AuthenticationTrait;
    
    public function testUserCanAccessAccount()
    {
        $client = self::createClient();
        $this->loadTestUserFixtures($client->getContainer());
        $this->login($this->getTestUser($client->getContainer())->setRoles(['ROLE_USER']), $client);

        $client->request('GET', $client->getContainer()->get(UrlGeneratorInterface::class)->generate('account_index'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    public function testAnonymousCannotAccessAccount()
    {
        $client = self::createClient();
        $client->request('GET', $client->getContainer()->get(UrlGeneratorInterface::class)->generate('account_index'));
        $this->assertResponseRedirects($client->getContainer()->get(UrlGeneratorInterface::class)->generate('security_login'));
    }

}