<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Repository\EquipementRepository;
use App\Repository\MarqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AboutController extends AbstractController
{
    #[Route('/about', name: 'app_about')]
    public function index(
        ProduitRepository $produitRepository,
        EquipementRepository $equipementRepository,
        MarqueRepository $marqueRepository
    ): Response {
        return $this->render('about/index.html.twig', [
            'produits' => $produitRepository->findAll(),
            'equipements' => $equipementRepository->findAll(),
            'marques' => $marqueRepository->findAll(),
            'default_image' => 'default-product.jpg'
        ]);
    }

    #[Route('/eq', name: 'app_eq')]
    public function indexx(
        ProduitRepository $produitRepository,
        EquipementRepository $equipementRepository,
        MarqueRepository $marqueRepository
    ): Response {
        return $this->render('equipement/IndexEq.html.twig', [
            'produits' => $produitRepository->findAll(),
            'equipements' => $equipementRepository->findAll(),
            'marques' => $marqueRepository->findAll(),
            'default_image' => 'default-product.jpg'
        ]);
    }
}