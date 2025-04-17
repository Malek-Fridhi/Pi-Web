<?php

namespace App\Controller;

use App\Entity\MessengerMessage;
use App\Form\MessengerMessageType;
use App\Repository\MessengerMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/messenger/message')]
final class MessengerMessageController extends AbstractController
{
    #[Route(name: 'app_messenger_message_index', methods: ['GET'])]
    public function index(MessengerMessageRepository $messengerMessageRepository): Response
    {
        return $this->render('messenger_message/index.html.twig', [
            'messenger_messages' => $messengerMessageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_messenger_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $messengerMessage = new MessengerMessage();
        $form = $this->createForm(MessengerMessageType::class, $messengerMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($messengerMessage);
            $entityManager->flush();

            return $this->redirectToRoute('app_messenger_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('messenger_message/new.html.twig', [
            'messenger_message' => $messengerMessage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_messenger_message_show', methods: ['GET'])]
    public function show(MessengerMessage $messengerMessage): Response
    {
        return $this->render('messenger_message/show.html.twig', [
            'messenger_message' => $messengerMessage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_messenger_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MessengerMessage $messengerMessage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MessengerMessageType::class, $messengerMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_messenger_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('messenger_message/edit.html.twig', [
            'messenger_message' => $messengerMessage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_messenger_message_delete', methods: ['POST'])]
    public function delete(Request $request, MessengerMessage $messengerMessage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$messengerMessage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($messengerMessage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_messenger_message_index', [], Response::HTTP_SEE_OTHER);
    }
}
