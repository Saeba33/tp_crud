<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
final class DashboardAdminController extends AbstractController
{
    #[Route('/dashboardAdmin', name: 'app_dashboard_admin')]
    #[IsGranted("ROLE_ADMIN")]
    public function readUsers(UserRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('dashboard_admin/index.html.twig', [
            'controller_name' => 'DashboardAdminController',
            'users' => $users
        ]);
    }

    #[Route('/dashboardAdmin/update/{id}', name: 'app_dashboard_admin_update')]
    #[IsGranted("ROLE_ADMIN")]
    public function updateUser(UserRepository $userRepo, Request $request, EntityManagerInterface $emi, $id): Response
    {
        $user = $userRepo->find($id);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emi-> persist($user);
            $emi->flush();

            $this->addFlash('notice', 'Successfuly update');
            return $this->redirectToRoute('app_dashboard_admin');

        }

        return $this->render('dashboard_admin/update.html.twig', [
            'controller_name' => 'DashboardAdminController',
            'form' => $form->createView()
        ]);
    }


    #[Route('/dashboardAdmin/delete/{id}', name: 'app_dashboard_admin_delete')]
    #[IsGranted("ROLE_ADMIN")]
    public function deleteUser(EntityManagerInterface $emi, $id, UserRepository $usersRepo): Response
    {
        $user = $usersRepo->find($id);


        if ($user === $this->getUser()) {
            $this->addFlash('error', 'You cannot delete your own account while logged in.');
            return $this->redirectToRoute('app_dashboard_admin');

            $emi->remove($user);
            $emi->flush();

        }

        $this->addFlash('notice', 'Successfuly delete');
        return $this->redirectToRoute('app_dashboard_admin');
    }
}