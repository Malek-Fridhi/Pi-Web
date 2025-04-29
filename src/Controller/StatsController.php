<?php

namespace App\Controller;

use App\Repository\EvenementRepository;
use App\Repository\InscriptionevenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    #[Route('/admin/stats', name: 'app_stats')]
    public function index(
        EvenementRepository $eventRepo,
        InscriptionevenementRepository $inscriptionRepo
    ): Response {
        // Statistiques des événements
        $eventStats = [
            'total' => $eventRepo->count([]),
            'byStatus' => $eventRepo->countByStatus(),
            'capacityUsage' => $eventRepo->getCapacityUsageStats()
        ];

        // Statistiques des inscriptions
        $registrationStats = [
            'total' => $inscriptionRepo->count([]),
            'byStatus' => $inscriptionRepo->countByStatus(),
            'monthlyTrend' => $inscriptionRepo->getMonthlyTrend()
        ];

        return $this->render('admin/stats/index.html.twig', [
            'event_stats' => $eventStats,
            'registration_stats' => $registrationStats,
            'chart_data' => $this->prepareChartData($eventRepo, $inscriptionRepo)
        ]);
    }

    private function prepareChartData(
        EvenementRepository $eventRepo,
        InscriptionevenementRepository $inscriptionRepo
    ): array {
        // Données pour les graphiques
        return [
            'events_by_month' => $eventRepo->getEventsByMonth(),
            'registrations_by_month' => $inscriptionRepo->getRegistrationsByMonth(),
            'capacity_utilization' => $eventRepo->getCapacityUtilization()
        ];
    }

    #[Route('/admin/stats/events', name: 'app_stats_events')]
    public function eventStats(EvenementRepository $eventRepo): Response
    {
        return $this->render('admin/stats/_events.html.twig', [
            'stats' => $eventRepo->getDetailedStats()
        ]);
    }

    #[Route('/admin/stats/registrations', name: 'app_stats_registrations')]
    public function registrationStats(InscriptionevenementRepository $inscriptionRepo): Response
    {
        return $this->render('admin/stats/_registrations.html.twig', [
            'stats' => $inscriptionRepo->getDetailedStats()
        ]);
    }
}