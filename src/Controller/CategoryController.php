<?php

namespace App\Controller;

use App\Entity\CategoriaDenuncia;
use App\Form\CategoriaDenunciaType;
use App\Repository\CategoriaDenunciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/categories')]
#[IsGranted('ROLE_ADMIN')]
class CategoryController extends AbstractController
{
    private CategoriaDenunciaRepository $repository;

    public function __construct(CategoriaDenunciaRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/', name: 'category_index', methods: ['GET'])]
    public function index(): Response
    {
        $categories = $this->repository->findAll();

        return $this->render('category/index.html.twig', [
            'title' => 'Gestión de Categorías',
            'categories' => $categories,
        ]);
    }

    #[Route('/create', name: 'category_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new CategoriaDenuncia();
        $form = $this->createForm(CategoriaDenunciaType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Categoría creada exitosamente.');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/create.html.twig', [
            'title' => 'Crear Categoría',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'category_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = $this->repository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Categoría no encontrada.');
        }

        $form = $this->createForm(CategoriaDenunciaType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Categoría actualizada exitosamente.');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/edit.html.twig', [
            'title' => 'Editar Categoría',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'category_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = $this->repository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Categoría no encontrada.');
        }

        if ($this->isCsrfTokenValid('delete_category_' . $category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();

            $this->addFlash('success', 'Categoría eliminada exitosamente.');
        }

        return $this->redirectToRoute('category_index');
    }
}
