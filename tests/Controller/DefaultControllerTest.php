<?php
namespace App\Tests\Controller;

use App\Controller\DefaultController;
use App\Entity\User;
use App\Security\ToDoListAuthenticator;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[
    UsesClass(DefaultController::class),
    CoversClass(DefaultController::class),
    CoversClass(User::class),
    CoversClass(ToDoListAuthenticator::class),
]
class DefaultControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private EntityManager|null $em = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get('doctrine')->getManager();
    }

    public function testHomepageIsUp()
    {

        $userRepository = $this->em->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['username' => 'admin']);

        $urlGenerator = $this->client->getContainer()->get('router');
        $this->client->request(Request::METHOD_GET, $urlGenerator->generate('homepage'));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->loginUser($testUser);

        $this->client->request(Request::METHOD_GET, $urlGenerator->generate('homepage'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}