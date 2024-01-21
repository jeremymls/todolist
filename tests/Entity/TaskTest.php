<?php
namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use PHPUnit\Metadata\Covers;

#[UsesClass(Task::class)]
#[CoversClass(Task::class)]
class TaskTest extends TestCase
{
    private Task $task;

    protected function setUp(): void
    {
        $this->task = new Task();
    }

    public function testId()
    {
        $this->assertNull($this->task->getId());
    }

    public function testCreatedAt()
    {
        $this->task->setCreatedAt(new \Datetime());

        $this->assertInstanceOf(\Datetime::class, $this->task->getCreatedAt());
    }

    public function testTitle()
    {
        $this->task->setTitle('Sample title');

        $this->assertEquals('Sample title', $this->task->getTitle());
    }

    public function testContent()
    {
        $this->task->setContent('Sample content');

        $this->assertEquals('Sample content', $this->task->getContent());
    }

    public function testToggle()
    {
        $this->task->toggle(true);

        $this->assertTrue($this->task->isDone());

        $this->task->toggle(false);

        $this->assertFalse($this->task->isDone());
    }

    public function testUser()
    {
        $user = $this->createMock(User::class);
        $this->task->setUser($user);

        $this->assertInstanceOf(User::class, $this->task->getUser());
    }

}
?>