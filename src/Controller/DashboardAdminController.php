<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
final class DashboardAdminController extends AbstractController
{
    #[Route('/dashboardAdmin', name: 'app_dashboard_admin')]
    #[IsGranted("ROLE_ADMIN")]
    public function dashboardAdmin(UserRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('dashboard_admin/index.html.twig', [
            'controller_name' => 'DashboardAdminController',
            'users' => $users
        ]);
    }
}