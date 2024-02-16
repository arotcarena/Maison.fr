<?php
namespace App\Tests\Controller;

use App\DataFixtures\TestUserFixtures;
use App\DataFixtures\TestUsersFixtures;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Form;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

class SecurityControllerTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testLoginRedirectToHomeWithFlashMessage()
    {
        $client = self::createClient();
        
        $this->loadTestUsersFixtures($client->getContainer());

        $crawler = $client->request('GET', $client->getContainer()->get(UrlGeneratorInterface::class)->generate('security_login'));
        $form = $crawler->selectButton('Connexion')->form([
            'email' => TestUsersFixtures::USER_CONFIRMED_EMAIL,
            'password' => TestUsersFixtures::PASSWORD
        ]);
        $client->submit($form);
        $this->assertResponseRedirects($client->getContainer()->get(UrlGeneratorInterface::class)->generate('home'));
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success', 'Il manque le flash pour dire qu\'on est bien connecté');
    }

    public function testLoginWithGoodCredentialsButUnconfirmedUser()
    {
        $client = self::createClient();
        $this->loadTestUsersFixtures($client->getContainer());

        $this->assertFalse($this->isLogged($client), 'déjà logged in avant le test');

        $crawler = $client->request('GET', $client->getContainer()->get(UrlGeneratorInterface::class)->generate('security_login'));
        $form = $crawler->selectButton('Connexion')->form([
            'email' => TestUsersFixtures::USER_EMAIL,
            'password' => TestUsersFixtures::PASSWORD
        ]);
        $client->submit($form);
        $client->followRedirect();

        $this->assertFalse($this->isLogged($client), 'Logged In alors que le user est unconfirmed');
    }

    public function testLoginWithConfirmedUserButBadCredentials()
    {
        $client = self::createClient();
        $this->loadTestUsersFixtures($client->getContainer());

        $this->assertFalse($this->isLogged($client), 'déjà logged in avant le test');

        $crawler = $client->request('GET', $client->getContainer()->get(UrlGeneratorInterface::class)->generate('security_login'));
        $form = $crawler->selectButton('Connexion')->form([
            'email' => TestUsersFixtures::USER_CONFIRMED_EMAIL,
            'password' => 'badpassword'
        ]);
        $client->submit($form);
        $client->followRedirect();
        
        $this->assertFalse($this->isLogged($client), 'Logged In alors que les identifiants sont faux');
    }

    public function testLoginWithGoodCredentialsAndConfirmedUser()
    {
        $client = self::createClient();
        $this->loadTestUsersFixtures($client->getContainer());

        $this->assertFalse($this->isLogged($client), 'déjà logged in avant le test');

        $crawler = $client->request('GET', $client->getContainer()->get(UrlGeneratorInterface::class)->generate('security_login'));
        $form = $crawler->selectButton('Connexion')->form([
            'email' => TestUsersFixtures::USER_CONFIRMED_EMAIL,
            'password' => TestUsersFixtures::PASSWORD
        ]);
        $client->submit($form);
        $client->followRedirect();

        $this->assertTrue($this->isLogged($client), 'échec de connexion');
    }

    

    public function testLoginWithGoodCredentialsAndConfirmedUserButNeeds2fa()
    {
        $client = self::createClient();
        
        $this->loadTestUsersFixtures($client->getContainer());
        
        $this->assertFalse($this->isLogged($client), 'déjà connecté avant le test');

        $crawler = $client->request('GET', $client->getContainer()->get(UrlGeneratorInterface::class)->generate('security_login'));
        $form = $crawler->selectButton('Connexion')->form([
            'email' => TestUsersFixtures::USER_CONFIRMED_WITH_2FA_EMAIL,
            'password' => TestUsersFixtures::PASSWORD
        ]);
        $client->submit($form);
        $this->assertFalse($this->isLogged($client), 'logged in alors que le 2fa nest pas encore soumis');
    }

    public function testLoginWithGoodCredentialsAndConfirmedUserWith2faNeededAndSubmited()
    {
        $client = self::createClient();
        
        $this->loadTestUsersFixtures($client->getContainer());

        $this->assertFalse($this->isLogged($client), 'déjà connecté avant le test');

        $session = $client->getRequest()->getSession();
        $session->set(Security::AUTHENTICATION_ERROR, new AuthenticationException('', AppAuthenticator::APP_2FA_ERROR));
        $session->save();
        $crawler = $client->request('GET', $client->getContainer()->get(UrlGeneratorInterface::class)->generate('security_login'));
        
        $form = $crawler->selectButton('Connexion')->form([
            'email' => TestUsersFixtures::USER_CONFIRMED_WITH_2FA_AND_CODE_RECEIVED_EMAIL,
            'password' => TestUsersFixtures::PASSWORD,
            '2fa' => TestUsersFixtures::CODE_2FA
        ]);
        $client->submit($form);

        $this->assertTrue($this->isLogged($client), 'échec de connexion');
    }

    public function testLogoutRedirectToHomeWithFlashMessage()
    {
        $client = self::createClient();
        $container = $client->getContainer();
        $urlGenerator = $container->get(UrlGeneratorInterface::class);
        $this->loadTestUsersFixtures($container);
        $user = $this->getTestUser($container);
        
        $this->login($user, $client);

        $client->request('GET', $urlGenerator->generate('security_logout'));
        $this->assertResponseRedirects('http://localhost'. $urlGenerator->generate('home'));
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testLogoutDoLogout()
    {
        $client = self::createClient();
        $container = $client->getContainer();
        $urlGenerator = $container->get(UrlGeneratorInterface::class);
        $this->loadTestUsersFixtures($container);
        $user = $this->getTestUser($container);
        
        $this->login($user, $client);
        $this->assertTrue($this->isLogged($client), 'déjà logged out avant le test');

        $client->request('GET', $urlGenerator->generate('security_logout'));
        $client->followRedirect();
        $this->assertFalse($this->isLogged($client), 'échec de déconnexion');
    }

    
}