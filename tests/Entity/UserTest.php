<?php
namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[
    UsesClass(User::class),
    CoversClass(User::class)
]
class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testId()
    {
        $this->assertNull($this->user->getId());
    }

    public function testUsername()
    {
        $this->user->setUsername('Sample username');

        $this->assertEquals('Sample username', $this->user->getUsername());
        $this->assertEquals('Sample username', $this->user->getUserIdentifier());
    }

    public function testSalt()
    {
        $this->assertNull($this->user->getSalt());
    }

    public function testPassword()
    {
        $this->user->setPassword('Sample password');

        $this->assertEquals('Sample password', $this->user->getPassword());
    }

    public function testEmail()
    {
        $this->user->setEmail('Sample email');

        $this->assertEquals('Sample email', $this->user->getEmail());
    }

    public function testRoles()
    {
        $this->assertEquals(['ROLE_USER'], $this->user->getRoles());
        $this->user->setRoles(['ROLE_ADMIN']);

        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $this->user->getRoles());
    }

    public function testEraseCredentials()
    {
        $this->assertNull($this->user->eraseCredentials());
    }

    public function testUserTasks()
    {
        $this->assertEmpty($this->user->getTasks());
        $task = $this->createMock(Task::class);
        $task->method('getUser')->willReturn($this->user);
        $this->user->addTask($task);

        $this->assertInstanceOf(Task::class, $this->user->getTasks()[0]);

        $this->user->removeTask($task);

        $this->assertEmpty($this->user->getTasks());
    }

    public function testToString()
    {
        $this->user->setUsername('Sample username');
        $this->assertEquals('Sample username', $this->user->__toString());
    }

}