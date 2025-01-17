<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    #[Route('/error/404', name: 'error_404', methods: ['GET'])]
    public function error404(): Response
    {
        return $this->render('error/404.html.twig', [
            'title' => 'Página no encontrada',
        ]);
    }

    #[Route('/error/500', name: 'error_500', methods: ['GET'])]
    public function error500(): Response
    {
        return $this->render('error/500.html.twig', [
            'title' => 'Error interno del servidor',
        ]);
    }

    // Controlador de fallback para errores no manejados
    #[Route('/error/unexpected', name: 'error_unexpected', methods: ['GET'])]
    public function unexpectedError(): Response
    {
        return $this->render('error/unexpected.html.twig', [
            'title' => 'Error inesperado',
        ]);
    }
}
