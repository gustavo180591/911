<?php

namespace App\Controller;

use App\Repository\EstadisticaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/statistics')]
#[IsGranted('ROLE_ADMIN')]
class StatisticsController extends AbstractController
{
    private EstadisticaRepository $repository;

    public function __construct(EstadisticaRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/', name: 'statistics_index', methods: ['GET'])]
    public function index(): Response
    {
        // Obtener todas las estadísticas
        $statistics = $this->repository->findAll();

        return $this->render('statistics/index.html.twig', [
            'title' => 'Estadísticas del Sistema',
            'statistics' => $statistics,
        ]);
    }

    #[Route('/details/{id}', name: 'statistics_details', methods: ['GET'])]
    public function details(int $id): Response
    {
        // Obtener una estadística específica por ID
        $statistic = $this->repository->find($id);

        if (!$statistic) {
            throw $this->createNotFoundException('La estadística solicitada no existe.');
        }

        return $this->render('statistics/details.html.twig', [
            'title' => 'Detalle de Estadística',
            'statistic' => $statistic,
        ]);
    }

    #[Route('/generate', name: 'statistics_generate', methods: ['POST'])]
    public function generate(): Response
    {
        // Lógica para generar estadísticas (mock-up o implementación específica)

        // Simulación de generación de estadísticas (reemplazar con lógica real)
        // Ejemplo: Agregar estadísticas generadas automáticamente.

        $this->addFlash('success', 'Estadísticas generadas exitosamente.');

        return $this->redirectToRoute('statistics_index');
    }
}
