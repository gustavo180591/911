<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si el usuario ya está logueado, redirigir a la página principal
        if ($this->getUser()) {
            return $this->redirectToRoute('home_index');
        }

        // Obtener el error de login si hay
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Último nombre de usuario ingresado
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Este método puede estar vacío, Symfony lo gestiona automáticamente
        throw new \Exception('No se debe llegar a este método');
    }
}
