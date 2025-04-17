<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashComptController extends AbstractController
{
    #[Route('/dashcompt', name: 'app_dash_compt')]
    public function index(): Response
    {
        return $this->render('dash_compt/index.html.twig', [
            'controller_name' => 'DashComptController',
        ]);
    }
    

    /*#[Route('/dashcompt', name: 'app_dash_compt')]
    #[IsGranted('ROLE_COMPTABLE')]
    public function adminDashboard(): Response
    {
        return $this->render('dash_compt/index.html.twig');
    }*/
    #[Route('/dashcompt', name: 'comptable_dashboard')]
    #[IsGranted('ROLE_Comptable')]
    public function indexx(): Response
    {
        return $this->render('dash_compt/index.html.twig');
    }
}
