<?php

namespace App\Controller;

use App\Entity\Reporte;
use App\Form\ReporteType;
use App\Repository\ReporteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reports')]
#[IsGranted('ROLE_ADMIN')]
class ReportController extends AbstractController
{
    private ReporteRepository $repository;

    public function __construct(ReporteRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/', name: 'report_index', methods: ['GET'])]
    public function index(): Response
    {
        $reports = $this->repository->findBy([], ['fechaGeneracion' => 'DESC']);

        return $this->render('report/index.html.twig', [
            'title' => 'Listado de Reportes',
            'reports' => $reports,
        ]);
    }

    #[Route('/create', name: 'report_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $report = new Reporte();
        $form = $this->createForm(ReporteType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $report->setAutor($this->getUser());
            $report->setFechaGeneracion(new \DateTime());

            $entityManager->persist($report);
            $entityManager->flush();

            $this->addFlash('success', 'Reporte creado exitosamente.');

            return $this->redirectToRoute('report_index');
        }

        return $this->render('report/create.html.twig', [
            'title' => 'Crear Reporte',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/view/{id}', name: 'report_view', methods: ['GET'])]
    public function view(int $id): Response
    {
        $report = $this->repository->find($id);

        if (!$report) {
            throw $this->createNotFoundException('El reporte solicitado no existe.');
        }

        return $this->render('report/view.html.twig', [
            'title' => 'Detalle del Reporte',
            'report' => $report,
        ]);
    }

    #[Route('/delete/{id}', name: 'report_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $report = $this->repository->find($id);

        if (!$report) {
            throw $this->createNotFoundException('El reporte solicitado no existe.');
        }

        if ($this->isCsrfTokenValid('delete_report_' . $report->getId(), $request->request->get('_token'))) {
            $entityManager->remove($report);
            $entityManager->flush();

            $this->addFlash('success', 'Reporte eliminado exitosamente.');
        }

        return $this->redirectToRoute('report_index');
    }
}
