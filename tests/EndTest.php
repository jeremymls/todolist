<?php
namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EndTest extends WebTestCase
{
    private ?KernelBrowser $client;

    public function testEnd(): void
    {
        $this->client = static::createClient();
        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $userRepository = $em->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['username' => 'Test_edit(modified)' ]);
        $em->remove($testUser);
        $em->flush();
    }
}
