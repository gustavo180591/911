<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegalController extends AbstractController
{
    #[Route('/legal/terms', name: 'app_terms')]
    public function terms(): Response
    {
        return $this->render('legal/terms.html.twig');
    }

    #[Route('/legal/privacy', name: 'app_privacy')]
    public function privacy(): Response
    {
        // Esta ruta se usará más adelante cuando creemos la política de privacidad
        return $this->render('legal/privacy.html.twig');
    }
} 