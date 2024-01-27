<?php
namespace App\Tests;

use App\DataFixtures\AppFixtures;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StartTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private EntityManager|null $em = null;
    private UserPasswordHasherInterface $hasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get('doctrine')->getManager();
        $this->hasher = $this->client->getContainer()->get('security.user_password_hasher');

    }

    public function testStart(): void
    {
        $fixtures = new AppFixtures($this->hasher);
        $fixtures->load($this->em);
    }
}
