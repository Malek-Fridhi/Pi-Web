<?php

namespace App\Controller;

use App\Entity\Depense;
use App\Entity\Finance;
use App\Entity\RapportDepense;
use App\Entity\RapportRevenu;
use App\Form\DepenseType;
use App\Repository\DepenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/depenseadmin')]
final class DepenseAdminController extends AbstractController
{
    #[Route(name: 'app_depense_indexadmin', methods: ['GET'])]
    public function index(DepenseRepository $depenseRepository): Response
    {
        return $this->render('depense/indexadmin.html.twig', [
            'depenses' => $depenseRepository->findAll(),
        ]);
    }

   
}
