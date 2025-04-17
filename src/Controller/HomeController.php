<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ServiceRepository;
use App\Repository\FinanceRepository;
use App\Entity\Finance;

final class HomeController extends AbstractController
{
   /* #[Route('/', name: 'app_home')]
    public function index(ServiceRepository $serviceRepository,FinanceRepository $financeRepo): Response
    {$now = new \DateTime();
        $oneYearAgo = (clone $now)->modify('-12 months');
        
        $finances = $financeRepo->createQueryBuilder('f')
            ->where('f.anneeFin = :year OR f.anneeFin = :lastYear')
            ->setParameter('year', $now->format('Y'))
            ->setParameter('lastYear', $oneYearAgo->format('Y'))
            ->orderBy('f.anneeFin', 'ASC')
            ->addOrderBy('f.moisFin', 'ASC')
            ->getQuery()
            ->getResult();
            $totalProfit = 0;
    $monthlyProfits = [];
    
    foreach ($finances as $finance) {
        $totalProfit += $finance->getProfit();
        $monthlyProfits[] = [
            'month' => $finance->getMoisFin(),
            'year' => $finance->getAnneeFin(),
            'profit' => $finance->getProfit()
        ];
    }
    
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'services' => $serviceRepository->findAll(),
            'finances' => $finances,
            'totalProfit' => $totalProfit,
            'monthlyProfits' => $monthlyProfits,
        ]);
    }*/

    #[Route('SERVVVVVice', name: 'app_servicceeee')]
    public function indexx(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/services.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }
    #[Route('/', name: 'app_home')]
    public function index(ServiceRepository $serviceRepository,FinanceRepository $financeRepo): Response
    {$now = new \DateTime();
        $oneYearAgo = (clone $now)->modify('-12 months');
        
        $finances = $financeRepo->createQueryBuilder('f')
            ->where('f.anneeFin = :year OR f.anneeFin = :lastYear')
            ->setParameter('year', $now->format('Y'))
            ->setParameter('lastYear', $oneYearAgo->format('Y'))
            ->orderBy('f.anneeFin', 'ASC')
            ->addOrderBy('f.moisFin', 'ASC')
            ->getQuery()
            ->getResult();
            $totalProfit = 0;
    $monthlyProfits = [];
    
    foreach ($finances as $finance) {
        $totalProfit += $finance->getProfit();
        $monthlyProfits[] = [
            'month' => $finance->getMoisFin(),
            'year' => $finance->getAnneeFin(),
            'profit' => $finance->getProfit()
        ];
    }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'services' => $serviceRepository->findAll(),
            'finances' => $finances,
        'totalProfit' => $totalProfit,
        'monthlyProfits' => $monthlyProfits,
        ]);
    }

}