<?php

namespace App\Controller;

use App\Entity\EstadoDenuncia;
use App\Form\EstadoDenunciaType;
use App\Repository\EstadoDenunciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/statuses')]
#[IsGranted('ROLE_ADMIN')]
class StatusController extends AbstractController
{
    private EstadoDenunciaRepository $repository;

    public function __construct(EstadoDenunciaRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/', name: 'status_index', methods: ['GET'])]
    public function index(): Response
    {
        $statuses = $this->repository->findAll();

        return $this->render('status/index.html.twig', [
            'title' => 'Gestión de Estados de Denuncia',
            'statuses' => $statuses,
        ]);
    }

    #[Route('/create', name: 'status_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $status = new EstadoDenuncia();
        $form = $this->createForm(EstadoDenunciaType::class, $status);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($status);
            $entityManager->flush();

            $this->addFlash('success', 'Estado creado exitosamente.');

            return $this->redirectToRoute('status_index');
        }

        return $this->render('status/create.html.twig', [
            'title' => 'Crear Estado',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'status_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $status = $this->repository->find($id);

        if (!$status) {
            throw $this->createNotFoundException('El estado solicitado no existe.');
        }

        $form = $this->createForm(EstadoDenunciaType::class, $status);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Estado actualizado exitosamente.');

            return $this->redirectToRoute('status_index');
        }

        return $this->render('status/edit.html.twig', [
            'title' => 'Editar Estado',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'status_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $status = $this->repository->find($id);

        if (!$status) {
            throw $this->createNotFoundException('El estado solicitado no existe.');
        }

        if ($this->isCsrfTokenValid('delete_status_' . $status->getId(), $request->request->get('_token'))) {
            $entityManager->remove($status);
            $entityManager->flush();

            $this->addFlash('success', 'Estado eliminado exitosamente.');
        }

        return $this->redirectToRoute('status_index');
    }
}
