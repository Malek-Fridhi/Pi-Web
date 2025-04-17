<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ServiceRepository;
use App\Entity\Service;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

/*    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();

        $user = $this->getUser();
        if (!$user) {
            // Handle case where user is not logged in
            $this->addFlash('error', 'Vous devez être connecté pour créer une réclamation.');
            return $this->redirectToRoute('app_login');
        }
        $reservation->setUser($user);
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }*/

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ServiceRepository $serviceRepository): Response
    {
        $reservation = new Reservation();
    
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour réserver un service.');
            return $this->redirectToRoute('app_login');
        }
    
        $reservation->setUser($user);
    
        // 🆕 جلب id_service من الرابط
        $idService = $request->query->get('id_service');
        if ($idService) {
            $service = $serviceRepository->find($idService);
            if ($service) {
                $reservation->setService($service); // استدعي الدالة المناسبة حسب العلاقة
            }
        }
    
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }    

   /* #[Route('/{id_reservation}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }*/
    #[Route('/{id_reservation}', name: 'app_reservation_show', methods: ['GET'])]
public function show(int $id_reservation, ReservationRepository $reservationRepository): Response
{
    $reservation = $reservationRepository->find($id_reservation);
    if (!$reservation) {
        throw $this->createNotFoundException('Reservation not found.');
    }

    return $this->render('reservation/show.html.twig', [
        'reservation' => $reservation,
    ]);
}


  /*  #[Route('/{id_reservation}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }*/
    #[Route('/{id_reservation}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
public function edit(int $id_reservation, Request $request, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager): Response
{
    $reservation = $reservationRepository->find($id_reservation);
    
    if (!$reservation) {
        throw $this->createNotFoundException('Reservation not found.');
    }

    $form = $this->createForm(ReservationType::class, $reservation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('reservation/edit.html.twig', [
        'reservation' => $reservation,
        'form' => $form,
    ]);
}


  /*  #[Route('/{id_reservation}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId_reservation(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }*/
    #[Route('/{id_reservation}', name: 'app_reservation_delete', methods: ['POST'])]
public function delete(Request $request, int $id_reservation, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager): Response
{
    // Manually find the reservation by ID
    $reservation = $reservationRepository->find($id_reservation);

    // Check if the reservation exists
    if (!$reservation) {
        throw $this->createNotFoundException('Reservation not found.');
    }

    // Check CSRF token validity
    if ($this->isCsrfTokenValid('delete' . $reservation->getId_reservation(), $request->request->get('_token'))) {
        $entityManager->remove($reservation);
        $entityManager->flush(); // Save changes
    }

    return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
}

}
