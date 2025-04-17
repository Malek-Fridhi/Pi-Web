<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\UserRepository;

final class DashAdminController extends AbstractController
{
    #[Route('/dashadmin', name: 'app_dash_admin')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('dash_admin/index.html.twig', [
            'controller_name' => 'DashAdminController',
            'users' => $userRepository->findAll(),

        ]);
    }

    #[Route('/admin1', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminDashboard(UserRepository $userRepository): Response
    {
        return $this->render('dash_admin/index.html.twig',[
            'users' => $userRepository->findAll(),

        ]);
    }
}
