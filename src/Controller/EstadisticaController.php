<?php

namespace App\Controller;

use App\Entity\Denuncia;
use App\Entity\CategoriaDenuncia;
use App\Entity\EstadoDenuncia;
use App\Repository\DenunciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/estadisticas')]
#[IsGranted('ROLE_ADMIN')]
class EstadisticaController extends AbstractController
{
    public function __construct(
        private DenunciaRepository $denunciaRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'estadisticas_index')]
    public function index(): Response
    {
        // Obtener estadísticas por categoría
        $categorias = $this->entityManager->getRepository(CategoriaDenuncia::class)->findAll();
        $estadisticasCategorias = [];
        foreach ($categorias as $categoria) {
            $count = $this->denunciaRepository->count(['categoria' => $categoria]);
            $estadisticasCategorias[$categoria->getNombre()] = $count;
        }

        // Obtener estadísticas por estado
        $estados = $this->entityManager->getRepository(EstadoDenuncia::class)->findAll();
        $estadisticasEstados = [];
        foreach ($estados as $estado) {
            $count = $this->denunciaRepository->count(['estado' => $estado]);
            $estadisticasEstados[$estado->getNombre()] = $count;
        }

        // Obtener denuncias por mes
        $denunciasPorMes = $this->denunciaRepository->getDenunciasPorMes();

        // Obtener total de denuncias
        $totalDenuncias = $this->denunciaRepository->count([]);

        return $this->render('estadisticas/index.html.twig', [
            'estadisticasCategorias' => $estadisticasCategorias,
            'estadisticasEstados' => $estadisticasEstados,
            'denunciasPorMes' => $denunciasPorMes,
            'totalDenuncias' => $totalDenuncias,
        ]);
    }
} 