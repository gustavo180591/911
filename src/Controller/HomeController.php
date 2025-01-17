<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'title' => 'Bienvenido al Sistema 911',
        ]);
    }

    #[Route('/about', name: 'home_about', methods: ['GET'])]
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
            'title' => 'Acerca del Sistema 911',
            'description' => 'El Sistema 911 permite a los ciudadanos registrar y gestionar emergencias de forma rápida y segura.',
        ]);
    }

    #[Route('/contact', name: 'home_contact', methods: ['GET'])]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig', [
            'title' => 'Contáctanos',
            'email' => 'soporte@sistema911.com',
            'phone' => '+54 123 456 7890',
        ]);
    }
}
