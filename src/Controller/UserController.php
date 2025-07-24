<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user')]
    public function readUsers(UserRepository $userRepo, $id): Response
    {
        $user = $userRepo->find($id);

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user
        ]);
    }

    #[Route('/user/update/{id}', name: 'app_user_update')]
    public function updateUser(UserRepository $userRepo, Request $request, EntityManagerInterface $emi, $id): Response
    {
        $user = $userRepo->find($id);
        $form = $this->createForm(RegistrationFormType::class, $user, ['is_user' => true]);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emi-> persist($user);
            $emi->flush();

            $this->addFlash('notice', 'Successfuly update');

            return $this->redirectToRoute('app_user', ['id' => $user->getId()]);


        }

        return $this->render('user/update.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView()
        ]);
    }


    #[Route('/user/delete/{id}', name: 'app_user_delete')]
    public function deleteUser(EntityManagerInterface $emi, $id, UserRepository $usersRepo): Response
    {
        $user = $usersRepo->find($id);
        $emi->remove($user);
        $emi->flush();

        $this->addFlash('notice', 'Successfuly delete');
        return $this->redirectToRoute('app_home_page');
    }
}