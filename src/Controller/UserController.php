<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits pour accéder à cette page.')]
class UserController extends AbstractController
{
    private $em;

    public function __construct(private ManagerRegistry $registry)
    {
        $this->registry = $registry;
        $this->em = $this->registry->getManager();
    }

    #[Route(path: '/users', name: 'user_list')]
    public function listAction()
    {
        return $this->render('user/list.html.twig', ['users' => $this->registry->getRepository(User::class)->findAll()]);
    }

    #[Route(path: '/users/create', name: 'user_create')]
    public function createAction(Request $request, UserPasswordHasherInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/users/{id}/edit', name: 'user_edit')]
    public function editAction(User $user, Request $request, UserPasswordHasherInterface $passwordEncoder)
    {
        $form = $this->createForm(UserType::class, $user);
        $old_pass = $user->getPassword();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPassword()) {
                $password = $passwordEncoder->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
            } else {
                $user->setPassword($old_pass);
            }

            $this->em->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
