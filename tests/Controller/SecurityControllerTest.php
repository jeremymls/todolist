<?php
namespace App\Tests\Controller;

use App\Controller\DefaultController;
use App\Controller\SecurityController;
use App\Entity\User;
use App\Security\ToDoListAuthenticator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[
    UsesClass(SecurityController::class),
    CoversClass(SecurityController::class),
    CoversClass(ToDoListAuthenticator::class),
    UsesClass(User::class),
    UsesClass(DefaultController::class)
]
class SecurityControllerTest extends WebTestCase
{
    private ?KernelBrowser $client;
    private ?UrlGeneratorInterface $urlGenerator;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router');

    }

    public function testLogin(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('login'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testLoginWithBadCredentials(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('login'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Se connecter', [
            '_username' => 'badUsername',
            'password' => 'badPassword',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertRouteSame('login');
    }

    public function testLoginWithGoodCredentials(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('login'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Se connecter', [
            '_username' => 'admin',
            'password' => '123456',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertRouteSame('homepage');
    }

    public function testLogout(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('logout'));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // $this->client->followRedirect();
        // $this->assertRouteSame('homepage');
    }
}