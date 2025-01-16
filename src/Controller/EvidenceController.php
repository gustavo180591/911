<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EvidenceController extends AbstractController
{
    #[Route('/evidence', name: 'app_evidence')]
    public function index(): Response
    {
        return $this->render('evidence/index.html.twig', [
            'controller_name' => 'EvidenceController',
        ]);
    }
}
