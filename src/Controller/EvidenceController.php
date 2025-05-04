<?php

namespace App\Controller;

use App\Entity\Evidencia;
use App\Form\EvidenciaType;
use App\Repository\EvidenciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/evidence')]
#[IsGranted('ROLE_USER')]
class EvidenceController extends AbstractController
{
    private EvidenciaRepository $repository;

    public function __construct(EvidenciaRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/', name: 'evidence_index', methods: ['GET'])]
    public function index(): Response
    {
        // Si es admin, mostrar todas las evidencias
        if ($this->isGranted('ROLE_ADMIN')) {
            $evidences = $this->repository->findBy([], ['fechaSubida' => 'DESC']);
        } else {
            // Si es usuario normal, mostrar solo sus evidencias
        $evidences = $this->repository->findBy(['usuario' => $this->getUser()], ['fechaSubida' => 'DESC']);
        }

        return $this->render('evidence/index.html.twig', [
            'title' => 'Mis Evidencias',
            'evidences' => $evidences,
        ]);
    }

    #[Route('/create', name: 'evidence_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evidence = new Evidencia();
        $form = $this->createForm(EvidenciaType::class, $evidence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evidence->setUsuario($this->getUser());
            $evidence->setFechaSubida(new \DateTime());

            $entityManager->persist($evidence);
            $entityManager->flush();

            $this->addFlash('success', 'Evidencia subida exitosamente.');

            return $this->redirectToRoute('evidence_index');
        }

        return $this->render('evidence/create.html.twig', [
            'title' => 'Subir Evidencia',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/view/{id}', name: 'evidence_view', methods: ['GET'])]
    public function view(int $id): Response
    {
        // Si es admin, puede ver cualquier evidencia
        if ($this->isGranted('ROLE_ADMIN')) {
            $evidence = $this->repository->find($id);
        } else {
            // Si es usuario normal, solo puede ver sus evidencias
        $evidence = $this->repository->findOneBy(['id' => $id, 'usuario' => $this->getUser()]);
        }

        if (!$evidence) {
            throw $this->createNotFoundException('La evidencia solicitada no existe.');
        }

        return $this->render('evidence/view.html.twig', [
            'title' => 'Detalle de Evidencia',
            'evidence' => $evidence,
        ]);
    }

    #[Route('/delete/{id}', name: 'evidence_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Si es admin, puede eliminar cualquier evidencia
        if ($this->isGranted('ROLE_ADMIN')) {
            $evidence = $this->repository->find($id);
        } else {
            // Si es usuario normal, solo puede eliminar sus evidencias
        $evidence = $this->repository->findOneBy(['id' => $id, 'usuario' => $this->getUser()]);
        }

        if (!$evidence) {
            throw $this->createNotFoundException('La evidencia solicitada no existe.');
        }

        if ($this->isCsrfTokenValid('delete_evidence_' . $evidence->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evidence);
            $entityManager->flush();

            $this->addFlash('success', 'Evidencia eliminada exitosamente.');
        }

        return $this->redirectToRoute('evidence_index');
    }
}
