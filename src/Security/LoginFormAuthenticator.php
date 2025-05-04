<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    // Definir las rutas como constantes
    public const LOGIN_ROUTE = 'app_login';
    public const DEFAULT_SUCCESS_ROUTE = 'home_index';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('_username', '');
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username, function($userIdentifier) {
                // Buscar usuario por email, teléfono, username o DNI
                $user = $this->entityManager->getRepository(User::class)
                    ->createQueryBuilder('u')
                    ->where('u.email = :identifier')
                    ->orWhere('u.telefono = :identifier')
                    ->orWhere('u.username = :identifier')
                    ->orWhere('u.dni = :identifier')
                    ->setParameter('identifier', $userIdentifier)
                    ->getQuery()
                    ->getOneOrNullResult();

                if (!$user) {
                    throw new UserNotFoundException('Usuario no encontrado');
                }

                return $user;
            }),
            new PasswordCredentials($request->request->get('_password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Si hay una URL objetivo guardada (por ejemplo, de una página protegida), redirigir allí
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirigir a la página principal después del login exitoso
        return new RedirectResponse($this->urlGenerator->generate(self::DEFAULT_SUCCESS_ROUTE));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
