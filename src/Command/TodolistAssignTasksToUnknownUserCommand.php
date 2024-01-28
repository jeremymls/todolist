<?php

namespace App\Command;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'todolist:assign_tasks_to_unknown_user',
    description: 'Commande qui crée les utilisateurs "admin" et "Inconnu" et qui permet d\'assigner les tâches sans auteurs à l\'utilisateur Inconnu. Les mots de passe par défaut sont "123456".',
)]
class TodolistAssignTasksToUnknownUserCommand extends Command
{
    private $passwordHasher;
    private $em;

    public function __construct(UserPasswordHasherInterface $passwordHasher, private ManagerRegistry $registry)
    {
        parent::__construct();
        $this->passwordHasher = $passwordHasher;
        $this->em = $this->registry->getManager();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Assignation des tâches sans auteurs à un utilisateur inconnu');
        sleep(2);

        $need_flush = false;

        if (!$this->em->getRepository(User::class)->findOneBy(['username' => 'admin'])) {
            $io->section('Création de l\'utilisateur admin');
            $user = new User();
            $user->setUsername('admin');
            $user->setEmail('admin@email.fr');
            $user->setPassword($this->passwordHasher->hashPassword($user, '123456'));
            $user->setRoles(['ROLE_ADMIN']);
            $this->em->persist($user);
            $need_flush = true;
            $io->success('Utilisateur admin créé.');
        } else {
            $io->info('L\'utilisateur admin existe déjà.');
        }
        sleep(1);

        if (!$this->em->getRepository(User::class)->findOneBy(['username' => 'Inconnu'])) {
            $io->section('Création de l\'utilisateur Inconnu');
            $user = new User();
            $user->setUsername('Inconnu');
            $user->setEmail('inconnu@test.fr');
            $user->setPassword($this->passwordHasher->hashPassword($user, '123456'));
            $this->em->persist($user);
            $need_flush = true;
            $io->success('Utilisateur Inconnu créé.');
        } else {
            $io->info('L\'utilisateur Inconnu existe déjà.');
        }
        sleep(1);

        if ($need_flush) {
            $this->em->flush();
            $io->warning('Pensez à changer les mots de passe: 123456');
        }

        $tasks = $this->registry->getRepository(Task::class)->findBy(['user' => null]);

        if (count($tasks) > 0) {
            $io->section('Assignation des tâches');

            $io->writeln('Nombre de tâches sans auteurs: ' . count($tasks));
            $io->progressStart(count($tasks));

            $unknownUser = $this->registry->getRepository(User::class)->findOneBy(['username' => 'Inconnu']);
            foreach ($tasks as $task) {
                $task->setUser($unknownUser);
                $this->em->persist($task);
                $io->progressAdvance();
                usleep(200000);
            }
            $this->em->flush();
            $io->progressFinish();

            $io->success('Tâches assignées.');

            return Command::SUCCESS;
        } else {
            $io->success('Il n\'y a pas de tâches sans auteurs.');
            return Command::SUCCESS;
        }
    }
}
