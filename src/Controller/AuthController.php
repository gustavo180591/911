<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/auth')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'auth_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): Response {
        $user = new Usuario();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Cifrar la contraseña
            $password = password_hash($form->get('plainPassword')->getData(), PASSWORD_BCRYPT);
            $user->setPassword($password);
            $user->setFechaRegistro(new \DateTime());

            $entityManager->persist($user);
            $entityManager->flush();

            // Autenticar al usuario tras el registro
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'auth_login', methods: ['GET', 'POST'])]
    public function login(): Response
    {
        // Symfony gestiona automáticamente el login con el sistema de seguridad configurado
        if ($this->getUser()) {
            return $this->redirectToRoute('home'); // Redirigir al usuario si ya está autenticado
        }

        return $this->render('auth/login.html.twig', [
            'error' => null, // Puedes pasar errores de autenticación aquí
        ]);
    }

    #[Route('/logout', name: 'auth_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Symfony gestiona automáticamente el logout
        throw new \LogicException('Este método puede estar vacío, Symfony lo gestiona automáticamente.');
    }
}
