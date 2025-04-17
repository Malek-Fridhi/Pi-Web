<?php

namespace App\Controller;

use App\Entity\Cour;
use App\Form\CourType;
use App\Repository\CourRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cou')]
final class courControllerFront extends AbstractController
{
    #[Route(name: 'app_cour_indexx', methods: ['GET'])]
    public function index(CourRepository $courRepository): Response
    {
        return $this->render('cour/indexFront.html.twig', [
            'cours' => $courRepository->findAll(),
        ]);
    }

   
   
   
   
   
}
