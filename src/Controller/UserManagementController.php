<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]
class UserManagementController extends AbstractController
{
    private UsuarioRepository $repository;

    public function __construct(UsuarioRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/', name: 'user_management_index', methods: ['GET'])]
    public function index(): Response
    {
        $users = $this->repository->findAll();

        return $this->render('user_management/index.html.twig', [
            'title' => 'Gestión de Usuarios',
            'users' => $users,
        ]);
    }

    #[Route('/create', name: 'user_management_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new Usuario();
        $form = $this->createForm(UsuarioType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
            $user->setFechaRegistro(new \DateTime());

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Usuario creado exitosamente.');

            return $this->redirectToRoute('user_management_index');
        }

        return $this->render('user_management/create.html.twig', [
            'title' => 'Crear Usuario',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'user_management_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->repository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('El usuario solicitado no existe.');
        }

        $form = $this->createForm(UsuarioType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Usuario actualizado exitosamente.');

            return $this->redirectToRoute('user_management_index');
        }

        return $this->render('user_management/edit.html.twig', [
            'title' => 'Editar Usuario',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'user_management_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->repository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('El usuario solicitado no existe.');
        }

        if ($this->isCsrfTokenValid('delete_user_' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Usuario eliminado exitosamente.');
        }

        return $this->redirectToRoute('user_management_index');
    }
}
