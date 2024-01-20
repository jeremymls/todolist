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
    description: 'Commande pour assigner les tâches sans auteurs à un utilisateur inconnu',
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
        $this
            ->addArgument('mail', InputArgument::REQUIRED, 'Adresse mail de l\'utilisateur inconnu')
            ->addArgument('pass', InputArgument::OPTIONAL, 'Mot de passe de l\'utilisateur inconnu (par défaut: "password")')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $mail = $input->getArgument('mail');
        $pass = $input->getArgument('pass') ?? 'password';


        $io->title('Assignation des tâches sans auteurs à un utilisateur inconnu');
        $io->writeln('Adresse mail: ' . $mail);
        $io->writeln('Mot de passe: ' . $pass);


        $user = $this->registry->getRepository(User::class)->findOneByUsername('Inconnu');
        if (null === $user) {
            $io->section('Création de l\'utilisateur');
            $user = new User();
            $user->setUsername('Inconnu');
            $user->setEmail($mail);
            $user->setPassword($this->passwordHasher->hashPassword($user, $pass));

            $this->em->persist($user);
            $this->em->flush();

            $io->success('Utilisateur créé.');
        } else {
            $io->info('L\'utilisateur existe déjà.');
        }

        $tasks = $this->registry->getRepository(Task::class)->findBy(['user' => null]);
        if (empty($tasks)) {
            $io->success('Il n\'y a pas de tâches sans auteurs.');
            return Command::SUCCESS;
        }
        $io->section('Assignation des tâches sans auteurs à l\'utilisateur');
        $io->writeln('Nombre de tâches sans auteurs: ' . count($tasks));
        $io->progressStart(count($tasks));
        foreach ($tasks as $task) {
            $task->setUser($user);
            $this->em->persist($task);
            $io->progressAdvance();
        }
        $this->em->flush();
        $io->progressFinish();

        $io->success('Tâches assignées.');

        return Command::SUCCESS;
    }
}
