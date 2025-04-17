<?php
namespace App\Controller;

use App\Entity\Objectif;
use App\Form\ObjectifType;
use App\Repository\ObjectifRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository; 

#[Route('/objectif')]
class ObjectifController extends AbstractController
{
  /*  #[Route('/', name: 'app_objectif_index', methods: ['GET'])]
    public function index(ObjectifRepository $objectifRepository): Response
    {
        return $this->render('objectif/index.html.twig', [
            'objectifs' => $objectifRepository->findAll(),
        ]);
    }*/
    #[Route(name: 'app_objectif_index', methods: ['GET'])]
 public function index(ObjectifRepository $objectifRepository): Response
 {
     return $this->render('objectif/index.html.twig', [
         'objectifs' => $objectifRepository->findAll(),
     ]);
 }

 /*   #[Route('/new', name: 'app_objectif_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $objectif = new Objectif();
        // Temporary solution - Retrieves user with ID 1
        $user = $userRepository->find(1);
        if (!$user) {
            // Create a temporary user if none exists
            $user = new User();
            $user->setEmail('temp@example.com');
            $user->setPassword(password_hash('temp123', PASSWORD_DEFAULT));
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('warning', 'A temporary user has been created (ID: 1)');
        }
        $objectif->setUser($user);
        $form = $this->createForm(ObjectifType::class, $objectif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($objectif);
            $entityManager->flush();
            $this->addFlash('success', 'Objective created successfully!');
            return $this->redirectToRoute('app_objectif_index');
        }

        return $this->render('objectif/new.html.twig', [
            'objectif' => $objectif,
            'form' => $form,
        ]);
    }*/

    #[Route('/new', name: 'app_objectif_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer un objectif.');
        }

        $objectif = new Objectif();
        $objectif->setUser($user);

        $form = $this->createForm(ObjectifType::class, $objectif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($objectif);
            $entityManager->flush();
            $this->addFlash('success', 'Objectif créé avec succès !');
            return $this->redirectToRoute('app_objectif_index');
        }

        return $this->render('objectif/new.html.twig', [
            'objectif' => $objectif,
            'form' => $form,
        ]);
    }


    #[Route('/{idObjectif}', name: 'app_objectif_show', methods: ['GET'])]
    public function show(int $idObjectif, ObjectifRepository $objectifRepository): Response
    {
        $objectif = $objectifRepository->find($idObjectif);
        if (!$objectif) {
            throw $this->createNotFoundException('Objective not found');
        }

        return $this->render('objectif/show.html.twig', [
            'objectif' => $objectif,
            'planNutritionnels' => $objectif->getPlanNutritionnels(),
        ]);
    }

    #[Route('/{idObjectif}/edit', name: 'app_objectif_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $idObjectif, ObjectifRepository $objectifRepository, EntityManagerInterface $entityManager): Response
    {
        $objectif = $objectifRepository->find($idObjectif);
        if (!$objectif) {
            throw $this->createNotFoundException('Objective not found');
        }

        $form = $this->createForm(ObjectifType::class, $objectif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Objective updated successfully!');
            return $this->redirectToRoute('app_objectif_index');
        }

        return $this->render('objectif/edit.html.twig', [
            'objectif' => $objectif,
            'form' => $form,
        ]);
    }

    #[Route('/{idObjectif}', name: 'app_objectif_delete', methods: ['POST'])]
    public function delete(Request $request, int $idObjectif, ObjectifRepository $objectifRepository, EntityManagerInterface $entityManager): Response
    {
        $objectif = $objectifRepository->find($idObjectif);
        if (!$objectif) {
            throw $this->createNotFoundException('Objective not found');
        }

        if ($this->isCsrfTokenValid('delete'.$objectif->getIdObjectif(), $request->request->get('_token'))) {
            $entityManager->remove($objectif);
            $entityManager->flush();
            $this->addFlash('success', 'Objective deleted successfully!');
        }

        return $this->redirectToRoute('app_objectif_index');
    }
}