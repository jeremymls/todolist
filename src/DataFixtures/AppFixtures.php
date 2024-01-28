<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // admin
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@test.fr');
        $user->setPassword($this->hasher->hashPassword($user, '123456'));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        // user
        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@test.fr');
        $user->setPassword($this->hasher->hashPassword($user, '123456'));
        $manager->persist($user);

        // other user
        $user = new User();
        $user->setUsername('otherUser');
        $user->setEmail('otherUser@test.fr');
        $user->setPassword($this->hasher->hashPassword($user, '123456'));
        $manager->persist($user);

        // unknown user
        $user = new User();
        $user->setUsername('Inconnu');
        $user->setEmail('inconnu@test.fr');
        $user->setPassword($this->hasher->hashPassword($user, '123456'));
        $manager->persist($user);

        $manager->flush();
    }
}
