<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

   /* #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }*/

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
{
    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Encode the password
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            )
        );

        // Handle the image upload
        $imageFile = $form->get('image')->getData();  // Get the uploaded image file
        if ($imageFile) {
            // Generate a unique filename for the image
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();

            // Move the file to the public/images directory
            $imageFile->move(
                $this->getParameter('kernel.project_dir') . '/public/images', // Save directly in public/images
                $newFilename
            );

            // Set the image filename in the User entity (you should have an `image` field in the User entity)
            $user->setImage($newFilename);  // Assuming you have an `image` field for the file name
        }

        // Get the roles from the form and set them
        $roles = $form->get('roles')->getData();
        $user->setRoles($roles);

        // Persist the user entity to the database
        $entityManager->persist($user);
        $entityManager->flush();

        // Redirect to the login page after successful registration
        return $this->redirectToRoute('app_login');
    }

    // Render the registration form if it's not submitted or is invalid
    return $this->render('user/new.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/{id}', name: 'app_user_show',requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }





    #[Route('/statistics', name: 'user_statistics')]
    public function statistics(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        $stats = [];
        foreach ($users as $user) {
            foreach ($user->getRoles() as $role) {
                if (!isset($stats[$role])) {
                    $stats[$role] = 0;
                }
                $stats[$role]++;
            }
        }

        return $this->render('user/statistics.html.twig', [
            'stats' => $stats
        ]);
    }
    #[Route('/statistics/pdf', name: 'user_statistics_pdf')]
    public function statisticsPdf(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
    
        $stats = [];
        foreach ($users as $user) {
            foreach ($user->getRoles() as $role) {
                if (!isset($stats[$role])) {
                    $stats[$role] = 0;
                }
                $stats[$role]++;
            }
        }
    
        // Render HTML for the PDF
        $html = $this->renderView('user/statistics_pdf.html.twig', [
            'stats' => $stats,
        ]);
    
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
    
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="user_statistics.pdf"',
        ]);
    }

/*    #[Route('/statistics/pdf/chart', name: 'user_statistics_pdf_with_chart', methods: ['POST'])]
public function statisticsPdfWithChart(Request $request, UserRepository $userRepository): Response
{
    $chartImage = $request->request->get('chartImage');

    $users = $userRepository->findAll();
    $stats = [];
    foreach ($users as $user) {
        foreach ($user->getRoles() as $role) {
            $stats[$role] = ($stats[$role] ?? 0) + 1;
        }
    }

    $html = $this->renderView('user/statistics_pdf_with_chart.html.twig', [
        'stats' => $stats,
        'chartImage' => $chartImage
    ]);

    $options = new \Dompdf\Options();
    $options->set('defaultFont', 'Arial');

    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return new Response($dompdf->output(), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="user_statistics_with_chart.pdf"',
    ]);
}*/

/*#[Route('/statistics/pdf/chart', name: 'user_statistics_pdf_with_chart', methods: ['POST'])]
public function statisticsPdfWithChart(Request $request, UserRepository $userRepository): Response
{
    // Get the base64 encoded image from the request
    $chartImage = $request->request->get('chartImage');

    // Decode the base64 image to binary data
    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $chartImage));

    // Generate a unique file name for the chart image
    $imagePath = $this->getParameter('kernel.project_dir') . '/public/images/chart_' . uniqid() . '.png';

    // Save the image to the server
    file_put_contents($imagePath, $imageData);

    // Get the statistics from the database
    $users = $userRepository->findAll();
    $stats = [];
    foreach ($users as $user) {
        foreach ($user->getRoles() as $role) {
            $stats[$role] = ($stats[$role] ?? 0) + 1;
        }
    }

    // Render HTML for the PDF (embed the chart image in the HTML)
    $html = $this->renderView('user/statistics_pdf_with_chart.html.twig', [
        'stats' => $stats,
        'chartImage' => $imagePath  // Pass the path of the chart image
    ]);

    // Set up Dompdf options
    $options = new \Dompdf\Options();
    $options->set('defaultFont', 'Arial');

    // Initialize Dompdf
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Return the PDF as a response
    return new Response($dompdf->output(), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="user_statistics_with_chart.pdf"',
    ]);
}*/

#[Route('/statistics/pdf/chart', name: 'user_statistics_pdf_with_chart', methods: ['POST'])]
public function generatePdfWithChart(Request $request, UserRepository $userRepository): Response
{
    // Handle the POST request from the form
    $chartImageData = $request->request->get('chartImage'); // Get base64 chart data
    
    // Decode the image
    list($type, $data) = explode(';', $chartImageData);
    list(, $data) = explode(',', $data);
    $imageData = base64_decode($data);

    // Save the image to a temporary file
    $imagePath = 'C:\xampp\htdocs\images';
    file_put_contents($imagePath, $imageData);

    // Generate the PDF as before
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);

    $dompdf = new Dompdf($options);

    // Render HTML content for the PDF
    $html = $this->renderView('user/statistics_pdf_with_chart.html.twig', [
        'chartImage' => $imagePath,  // Pass the image path to the view
        'users' => $userRepository->findAll(),
    ]);

    // Load HTML content into Dompdf
    $dompdf->loadHtml($html);

    // Set paper size and options
    $dompdf->setPaper('A4', 'portrait');

    // Render PDF
    $dompdf->render();

    // Output the PDF to the browser
    $dompdf->stream('user_statistics_with_chart.pdf');
}



}
