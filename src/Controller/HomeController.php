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
            'title' => 'Bienvenido al Sistema de Alerta Ciudadana Integrada',
        ]);
    }

    #[Route('/about', name: 'home_about', methods: ['GET'])]
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
            'title' => 'Acerca del Sistema de Alerta Ciudadana Integrada',
            'description' => 'El Sistema de Alerta Ciudadana Integrada Reporta incidentes de manera rápida y eficiente.
SACI conecta a ciudadanos y autoridades en toda
la provincia de Misiones..',
        ]);
    }

    #[Route('/contact', name: 'home_contact', methods: ['GET'])]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig', [
            'title' => 'CONTÁCTANOS',
            'email' => 'soporte@sistema911.com',
            'phone' => '+54 123 456 7890',
        ]);
    }
    #[Route('/contact/submit', name: 'contact_submit', methods: ['POST'])]
    public function submitContact(Request $request): Response
    {
        // Obtén los datos del formulario
        $data = $request->request->all();

        // Procesa los datos
        $this->addFlash('success', 'Gracias por contactarnos. Responderemos lo antes posible.');

        // Redirige a la página de contacto
        return $this->redirectToRoute('home_contact');
    }
}
