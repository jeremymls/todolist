<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function listAction(): Response
    {
        return $this->render('user/list.html.twig', ['users' => $this->registry->getRepository(User::class)->findAll()]);
    }

    #[Route(path: '/users/create', name: 'user_create')]
    public function createAction(Request $request, UserPasswordHasherInterface $passwordEncoder): Response
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
    public function editAction(User $user, Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $old_pass = $user->getPassword();
        $allValues = $request->request->all();
        if ($form->isSubmitted() && $form->isValid() && $allValues['user']['password']['first']) {
            $password = $passwordEncoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
        } else {
            $allValues['user']['password']['first'] = $old_pass;
            $allValues['user']['password']['second'] = $old_pass;
            $request->request->set('user', $allValues['user']);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
