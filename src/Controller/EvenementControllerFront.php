<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Inscriptionevenement;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/eventsx')]
class EvenementControllerFront extends AbstractController
{
    #[Route('/', name: 'app_evenement_front_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, EvenementRepository $evenementRepository): Response
    {
        $search = $request->query->get('q', '');
        $sort = $request->query->get('sort', 'date_asc');

        // Récupération de tous les événements
        $queryBuilder = $evenementRepository->createQueryBuilder('e');

        if (!empty($search)) {
            $queryBuilder
                ->andWhere('e.titre LIKE :search OR e.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Exécution de la requête sans le tri
        $allEvents = $queryBuilder->getQuery()->getResult();

        // Tri manuel comme dans l'ancienne version
        usort($allEvents, function($a, $b) use ($sort) {
            if ($sort === 'date_desc') {
                return $b->getDate() <=> $a->getDate();
            } else {
                return $a->getDate() <=> $b->getDate();
            }
        });

        // Pagination des résultats triés
        $pagination = $paginator->paginate(
            $allEvents,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('events/index.html.twig', [
            'pagination' => $pagination,
            'current_sort' => $sort,
            'search_query' => $search,
        ]);
    }

    #[Route('/detail/{idevenement}', name: 'app_evenement_front_show', methods: ['GET'])]
    public function show(int $idevenement, EvenementRepository $evenementRepository): Response
    {
        $evenement = $evenementRepository->find($idevenement);

        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        $isRegistered = false;

        if ($this->getUser()) {
            foreach ($evenement->getInscriptionevenements() as $inscription) {
                if ($inscription->getUser() === $this->getUser()) {
                    $isRegistered = true;
                    break;
                }
            }
        }

        $nbInscrits = count($evenement->getInscriptionevenements()->filter(function ($inscription) {
            return $inscription->getStatut() === 'Approved';
        }));

        $complet = ($evenement->getCapacite() && $nbInscrits >= $evenement->getCapacite());

        return $this->render('events/show.html.twig', [
            'evenement' => $evenement,
            'isRegistered' => $isRegistered,
            'complet' => $complet,
            'nb_inscrits' => $nbInscrits
        ]);
    }

    #[Route('/inscription/{idevenement}', name: 'app_evenement_front_inscription', methods: ['POST'])]
    public function inscription(int $idevenement, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour vous inscrire à un événement');
            return $this->redirectToRoute('app_login');
        }

        $evenement = $entityManager->getRepository(Evenement::class)->find($idevenement);

        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        $approvedCount = $entityManager->getRepository(Inscriptionevenement::class)->count([
            'evenement' => $evenement,
            'statut' => 'Approved',
        ]);

        if ($evenement->getCapacite() && $approvedCount >= $evenement->getCapacite()) {
            $this->addFlash('danger', 'La capacité maximale est atteinte pour cet événement.');
            return $this->redirectToRoute('app_evenement_front_show', ['idevenement' => $evenement->getIdevenement()]);
        }

        $existing = $entityManager->getRepository(Inscriptionevenement::class)->findOneBy([
            'evenement' => $evenement,
            'user' => $user,
        ]);

        if ($existing) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cet événement');
        } else {
            $inscription = new Inscriptionevenement();
            $inscription->setEvenement($evenement);
            $inscription->setUser($user);
            $inscription->setDateInscription(new \DateTime());
            $inscription->setStatut('Pending');

            $entityManager->persist($inscription);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription réussie !');
        }

        return $this->redirectToRoute('app_evenement_front_show', ['idevenement' => $evenement->getIdevenement()]);
    }

    #[Route('/desinscription/{idevenement}', name: 'app_evenement_front_desinscription', methods: ['POST'])]
    public function desinscription(int $idevenement, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour vous désinscrire');
            return $this->redirectToRoute('app_login');
        }

        $evenement = $entityManager->getRepository(Evenement::class)->find($idevenement);

        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        $inscription = $entityManager->getRepository(Inscriptionevenement::class)->findOneBy([
            'evenement' => $evenement,
            'user' => $user,
        ]);

        if ($inscription) {
            $entityManager->remove($inscription);
            $entityManager->flush();
            $this->addFlash('success', 'Désinscription réussie !');
        } else {
            $this->addFlash('warning', 'Vous n\'êtes pas inscrit à cet événement');
        }

        return $this->redirectToRoute('app_evenement_front_show', ['idevenement' => $evenement->getIdevenement()]);
    }
}