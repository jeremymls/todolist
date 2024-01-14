<?php
namespace App\Tests\Controller;

use App\Controller\TaskController;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
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
    UsesClass(TaskController::class),
    CoversClass(TaskController::class),
    CoversClass(ToDoListAuthenticator::class),
    CoversClass(TaskType::class),
    UsesClass(User::class),
    UsesClass(Task::class)
]
class TaskControllerTest extends WebTestCase
{
    private ?KernelBrowser $client;
    private ?EntityManager $em;
    private ?UrlGeneratorInterface $urlGenerator;
    private User $testUser;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get('doctrine')->getManager();
        $this->urlGenerator = $this->client->getContainer()->get('router');
        $userRepository = $this->em->getRepository(User::class);
        $this->testUser = $userRepository->findOneBy(['username' => 'Test2']);
        $this->client->loginUser($this->testUser);
    }

    public function testTaskList(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testTask(): void
    {
        // Create
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_create'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Ajouter', [
            'task[title]' => 'Test',
            'task[content]' => 'Test',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertRouteSame('task_list');

        // Edit
        $taskRepository = $this->em->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['title' => 'Test']);
        $taskID = $task->getId();
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_edit', ['id' => $taskID]));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->submitForm('Modifier', [
            'task[title]' => 'Test_edit',
            'task[content]' => 'Test_edit',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertRouteSame('task_list');

        // Toggle
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_toggle', ['id' => $taskID]));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertRouteSame('task_list');

        // Delete (with admin)
        $userRepository = $this->em->getRepository(User::class);
        $otherUser = $userRepository->findOneBy(['username' => 'Test']);
        $this->client->loginUser($otherUser);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_delete', ['id' => $taskID]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        // Delete (with user who doesn't own the task)
        $userRepository = $this->em->getRepository(User::class);
        $otherUser = $userRepository->findOneBy(['username' => 'Test3']);
        $this->client->loginUser($otherUser);
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_delete', ['id' => $taskID]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        // Delete (with user who own the task)
        $this->client->loginUser($this->testUser);

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_delete', ['id' => $taskID]));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertRouteSame('task_list');
    }
}