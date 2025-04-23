<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Inscriptionevenement;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/eventsx')]
class EvenementControllerFront extends AbstractController
{
    #[Route('/', name: 'app_evenement_front_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('events/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/{idevenement}', name: 'app_evenement_front_show', methods: ['GET'])]
    public function show(int $idevenement, EvenementRepository $evenementRepository): Response
    {
        $evenement = $evenementRepository->find($idevenement);
        
        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        return $this->render('events/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/inscription/{idevenement}', name: 'app_evenement_inscription', methods: ['POST'])]
    public function inscription(Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $existingInscription = $entityManager->getRepository(Inscriptionevenement::class)->findOneBy([
            'evenement' => $evenement,
            'iduser' => $user->getId()
        ]);

        if (!$existingInscription) {
            $inscription = new Inscriptionevenement();
            $inscription->setEvenement($evenement);
            $inscription->setIduser($user->getId());
            $inscription->setDateInscription(new \DateTime());
            $inscription->setStatut('Pending');

            $entityManager->persist($inscription);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription réussie !');
        }

        return $this->redirectToRoute('app_evenement_front_show', ['idevenement' => $evenement->getIdevenement()]);
    }
}