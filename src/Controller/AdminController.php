<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        // Aquí puedes agregar lógica para estadísticas o panel general.
        return $this->render('admin/dashboard.html.twig', [
            'title' => 'Panel Administrativo',
        ]);
    }

    #[Route('/users', name: 'admin_users', methods: ['GET'])]
    public function manageUsers(): Response
    {
        // Lógica para listar usuarios (puedes usar repositorios para obtener datos).
        return $this->render('admin/users.html.twig', [
            'title' => 'Gestión de Usuarios',
        ]);
    }

    #[Route('/categories', name: 'admin_categories', methods: ['GET'])]
    public function manageCategories(): Response
    {
        // Lógica para listar categorías.
        return $this->render('admin/categories.html.twig', [
            'title' => 'Gestión de Categorías',
        ]);
    }

    #[Route('/statuses', name: 'admin_statuses', methods: ['GET'])]
    public function manageStatuses(): Response
    {
        // Lógica para listar estados de emergencias.
        return $this->render('admin/statuses.html.twig', [
            'title' => 'Gestión de Estados',
        ]);
    }

    #[Route('/logs', name: 'admin_logs', methods: ['GET'])]
    public function viewLogs(): Response
    {
        // Lógica para mostrar logs del sistema.
        return $this->render('admin/logs.html.twig', [
            'title' => 'Registro de Auditoría',
        ]);
    }
}
