<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\LogSistemaRepository;

#[Route('/admin/logs')]
#[IsGranted('ROLE_ADMIN')]
class AuditLogController extends AbstractController
{
    private LogSistemaRepository $logSistemaRepository;

    public function __construct(LogSistemaRepository $logSistemaRepository)
    {
        $this->logSistemaRepository = $logSistemaRepository;
    }

    #[Route('/', name: 'audit_logs', methods: ['GET'])]
    public function index(): Response
    {
        // Obtener todos los logs desde el repositorio
        $logs = $this->logSistemaRepository->findBy([], ['fechaHora' => 'DESC']);

        return $this->render('audit_log/audit_logs.html.twig', [ // Cambiado a 'audit_log'
            'title' => 'Registro de Auditoría',
            'logs' => $logs,
        ]);
    }

    #[Route('/{id}', name: 'audit_log_detail', methods: ['GET'])]
    public function detail(int $id): Response
    {
        // Buscar un log específico por ID
        $log = $this->logSistemaRepository->find($id);

        if (!$log) {
            throw $this->createNotFoundException('El log solicitado no existe.');
        }

        return $this->render('audit_log/audit_log_detail.html.twig', [ // Cambiado a 'audit_log'
            'title' => 'Detalle del Log de Auditoría',
            'log' => $log,
        ]);
    }
}
