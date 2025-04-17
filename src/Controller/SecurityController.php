<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SecurityController extends AbstractController
{
  /*  #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }*/

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
   /* #[Route('/register', name: 'register')]
    public function index(): Response
    {
        return $this->render('security/register.html.twig');
    }*/

/*    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encoder le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            // Définir le rôle par défaut
            $user->setRoles(['ROLE_USER']);*/

            // Gérer le fichier image s’il existe
            /** @var UploadedFile $imageFile */
           /* $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/images',
                        $newFilename
                    );
                    $user->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l’image.');
                }
            }

            // Enregistrer l'utilisateur
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Inscription réussie ! Connectez-vous maintenant.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }*/
/*    #[Route('/register', name: 'app_register')]
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
    return $this->render('security/register.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
}
*/


#[Route('/register', name: 'app_register')]
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
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move(
                $this->getParameter('kernel.project_dir') . '/public/images',
                $newFilename
            );
            $user->setImage($newFilename);
        }

        // Set default role (remove the form->get('roles') line)
        $user->setRoles(['ROLE_Adherent']);  // Always set this role

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_login');
    }

    return $this->render('security/register.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
}


   /* #[Route('/connect/google', name: 'connect_google_start')]
    public function connectGoogle(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect();
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheck()
    {
        // Handled by the authenticator automatically
        // You can put logic here if you want to redirect or handle something manually
    }*/

    #[Route('/login/google', name: 'google_login')]
    public function loginWithGoogle(ClientRegistry $clientRegistry)
    {
        // Redirige vers Google OAuth2
        return $clientRegistry->getClient('google')->redirect();
    }

    #[Route('/login/google/check', name: 'google_check')]
    public function connectCheck()
    {
        // Ce contrôleur sera appelé après la redirection de Google
        // Tu peux récupérer ici l'utilisateur et le connecter à Symfony

        // Exemple : accéder aux informations de l'utilisateur
        $user = $this->getUser();
        // Gérer la connexion de l'utilisateur ou l'auto-inscription

        return $this->redirectToRoute('admin_dashboard');  // Ou rediriger où tu veux
    }


/*    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
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
    return $this->render('security/login.html.twig', [
        'form' => $form->createView(),
    ]);
}*/

#[Route('/login', name: 'app_login')]
public function login(AuthenticationUtils $authenticationUtils, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
{
    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $plaintextPassword = $form->get('password')->getData();
        $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
        $user->setPassword($hashedPassword);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('app_login'); // Redirect after register
    }

    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $error,
        'form' => $form->createView()
    ]);
}

}
