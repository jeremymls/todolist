<?php
namespace App\Tests\Controller;

use App\Controller\UserController;
use App\Entity\User;
use App\Form\UserType;
use App\Security\ToDoListAuthenticator;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[
    UsesClass(UserController::class),
    CoversClass(UserController::class),
    CoversClass(ToDoListAuthenticator::class),
    UsesClass(User::class),
    CoversClass(UserType::class)
]
class UserControllerTest extends WebTestCase
{
    private ?KernelBrowser $client;
    private ?EntityManager $em;
    private ?UrlGeneratorInterface $urlGenerator;
    private User $testUser;
    private $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get('doctrine')->getManager();
        $this->urlGenerator = $this->client->getContainer()->get('router');
        $this->userRepository = $this->em->getRepository(User::class);
        $this->testUser = $this->userRepository->findOneBy(['username' => 'Test']);
        $this->client->loginUser($this->testUser);
    }

    public function testListUsers(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testListUsersWithNoAdmin(): void
    {
        $user = $this->userRepository->findOneBy(['username' => 'Test2']);
        $this->client->loginUser($user);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_create'));
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_edit', ['id' => $this->testUser->getId()]));
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testUser(): void
    {
        // Create a new user
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_create'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Ajouter', [
            'user[username]' => 'Test_create',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'test@create.fr'
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertRouteSame('user_list');

        // Edit the new user with password
        $user = $this->userRepository->findOneBy(['username' => 'Test_create']);
        $userID = $user->getId();
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_edit', ['id' => $userID]));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Modifier', [
            'user[username]' => 'Test_edit(modified)',
            'user[password][first]' => 'password2',
            'user[password][second]' => 'password2',
            'user[email]' => 'test@edit.fr'
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertRouteSame('user_list');

        // Edit the new user without password
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_edit', ['id' => $userID]));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Modifier', [
            'user[username]' => 'Test_edit(modified)',
            'user[email]' => 'test2@edit.fr'
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertRouteSame('user_list');
    }
}