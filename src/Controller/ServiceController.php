<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/service')]
final class ServiceController extends AbstractController
{
    #[Route(name: 'app_service_index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($service);
            $entityManager->flush();

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('service/new.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

   /* #[Route('/{id_service}', name: 'app_service_show', methods: ['GET'])]
    public function show(Service $service): Response
    {
        return $this->render('service/show.html.twig', [
            'service' => $service,
        ]);
    }*/
    #[Route('/{id_service}', name: 'app_service_show', methods: ['GET'])]
public function show(int $id_service, ServiceRepository $serviceRepository): Response
{
    // Retrieve the service by its ID
    $service = $serviceRepository->find($id_service);

    // If the service doesn't exist, throw an exception
    if (!$service) {
        throw $this->createNotFoundException('Service not found.');
    }

    // Render the 'show' template with the service data
    return $this->render('service/show.html.twig', [
        'service' => $service,
    ]);
}

   /* #[Route('/{id_service}/edit', name: 'app_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('service/edit.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }*/
    #[Route('/{id_service}/edit', name: 'app_service_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, int $id_service, ServiceRepository $serviceRepository, EntityManagerInterface $entityManager): Response
{
    // Find the service by ID
    $service = $serviceRepository->find($id_service);
    
    // If service doesn't exist, throw an exception
    if (!$service) {
        throw $this->createNotFoundException('Service not found.');
    }

    // Create and handle the form for the service entity
    $form = $this->createForm(ServiceType::class, $service);
    $form->handleRequest($request);

    // If the form is submitted and valid, update the service
    if ($form->isSubmitted() && $form->isValid()) {
        // Persist the updated service data
        $entityManager->flush();

        // Redirect to the service index or a confirmation page
        return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
    }

    // Render the edit form if the form hasn't been submitted or isn't valid
    return $this->render('service/edit.html.twig', [
        'service' => $service,
        'form' => $form->createView(),
    ]);
}


  /*  #[Route('/{id_service}', name: 'app_service_delete', methods: ['POST'])]
    public function delete(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId_service(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($service);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
    }*/
    #[Route('/{id_service}', name: 'app_service_delete', methods: ['POST'])]
public function delete(Request $request, int $id_service, ServiceRepository $serviceRepository, EntityManagerInterface $entityManager): Response
{
    // Retrieve the service by its ID
    $service = $serviceRepository->find($id_service);

    // If the service doesn't exist, throw an exception
    if (!$service) {
        throw $this->createNotFoundException('Service not found.');
    }

    // Validate the CSRF token for deletion
    if ($this->isCsrfTokenValid('delete' . $service->getId_service(), $request->request->get('_token'))) {
        // Remove the service and save the changes
        $entityManager->remove($service);
        $entityManager->flush(); // Persist the changes to the database
    }

    // Redirect to the service index page after deletion
    return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
}

}
