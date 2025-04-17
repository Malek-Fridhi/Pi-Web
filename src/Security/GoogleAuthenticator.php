<?php
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;

class GoogleAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private ClientRegistry $clientRegistry,
        private EntityManagerInterface $em,
        private RouterInterface $router
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $client->getAccessToken();
        /** @var GoogleUser $googleUser */
        $googleUser = $client->fetchUserFromToken($accessToken);
        $email = $googleUser->getEmail();

        return new SelfValidatingPassport(
            new UserBadge($email, function() use ($googleUser) {
                $user = $this->em->getRepository(User::class)->findOneBy(['email' => $googleUser->getEmail()]);

                if (!$user) {
                    /*$user = new User();
                    $user->setEmail($googleUser->getEmail());
                    $user->setUsername($googleUser->getName());
                    $user->setGoogleId($googleUser->getId());
                    $user->setImage($googleUser->getAvatar());
                    $this->em->persist($user);
                    $this->em->flush();*/
                    $user = new User();
    $user->setEmail($googleUser->getEmail());
    $user->setUsername($googleUser->getName());
    $user->setImage($googleUser->getAvatar());
    $user->setTel('N/A');
    $user->setPassword('temporarypassword');  // Set a temporary password
    $user->setRoles(['ROLE_USER']);

    $this->em->persist($user);
    $this->em->flush();
                }

                return $user;
            })
        );
    }
  /*  public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $roles = $token->getUser()->getRoles();
    
        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($this->router->generate('admin_dashboard'));
        } elseif (in_array('ROLE_MANAGER', $roles)) {
            return new RedirectResponse($this->router->generate('manager_dashboard'));
        } elseif (in_array('ROLE_COACH', $roles)) {
            return new RedirectResponse($this->router->generate('coach_dashboard'));
        } elseif (in_array('ROLE_NUTRITIONIST', $roles)) {
            return new RedirectResponse($this->router->generate('nutritionist_dashboard'));
        } elseif (in_array('ROLE_ACCOUNTANT', $roles)) {
            return new RedirectResponse($this->router->generate('accountant_dashboard'));
        } elseif (in_array('ROLE_MEMBER', $roles)) {
            return new RedirectResponse($this->router->generate('member_dashboard'));
        }
    
        return new RedirectResponse($this->router->generate('user_dashboard'));
    }*/
    
   public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate('app_dash_admin'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }
}
