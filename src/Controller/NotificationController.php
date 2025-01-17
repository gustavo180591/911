<?php

namespace App\Controller;

use App\Entity\Notificacion;
use App\Repository\NotificacionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/notifications')]
#[IsGranted('ROLE_USER')]
class NotificationController extends AbstractController
{
    private NotificacionRepository $repository;

    public function __construct(NotificacionRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/', name: 'notification_index', methods: ['GET'])]
    public function index(): Response
    {
        // Obtener todas las notificaciones del usuario autenticado
        $notifications = $this->repository->findBy(['usuario' => $this->getUser()], ['fechaEnvio' => 'DESC']);

        return $this->render('notification/index.html.twig', [
            'title' => 'Mis Notificaciones',
            'notifications' => $notifications,
        ]);
    }

    #[Route('/view/{id}', name: 'notification_view', methods: ['GET'])]
    public function view(int $id): Response
    {
        $notification = $this->repository->findOneBy(['id' => $id, 'usuario' => $this->getUser()]);

        if (!$notification) {
            throw $this->createNotFoundException('La notificación solicitada no existe.');
        }

        return $this->render('notification/view.html.twig', [
            'title' => 'Detalle de Notificación',
            'notification' => $notification,
        ]);
    }

    #[Route('/delete/{id}', name: 'notification_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $notification = $this->repository->findOneBy(['id' => $id, 'usuario' => $this->getUser()]);

        if (!$notification) {
            throw $this->createNotFoundException('La notificación solicitada no existe.');
        }

        if ($this->isCsrfTokenValid('delete_notification_' . $notification->getId(), $request->request->get('_token'))) {
            $entityManager->remove($notification);
            $entityManager->flush();

            $this->addFlash('success', 'Notificación eliminada exitosamente.');
        }

        return $this->redirectToRoute('notification_index');
    }
}
