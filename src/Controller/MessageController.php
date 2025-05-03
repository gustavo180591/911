<?php

namespace App\Controller;

use App\Entity\Mensaje;
use App\Form\MensajeType;
use App\Repository\MensajeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/messages')]
#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    private MensajeRepository $repository;

    public function __construct(MensajeRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/', name: 'message_index', methods: ['GET'])]
    public function index(): Response
    {
        // Si es admin, mostrar todos los mensajes
        if ($this->isGranted('ROLE_ADMIN')) {
            $messages = $this->repository->findAll();
        } else {
            // Si es usuario normal, mostrar solo sus mensajes
            $messages = $this->repository->findByUser($this->getUser());
        }

        return $this->render('message/index.html.twig', [
            'title' => 'Mis Mensajes',
            'messages' => $messages,
        ]);
    }

    #[Route('/send', name: 'message_send', methods: ['GET', 'POST'])]
    public function send(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Mensaje();
        $form = $this->createForm(MensajeType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setUsuarioRemitente($this->getUser());
            $message->setFechaEnvio(new \DateTime());

            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Mensaje enviado exitosamente.');

            return $this->redirectToRoute('message_index');
        }

        return $this->render('message/send.html.twig', [
            'title' => 'Enviar Mensaje',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/view/{id}', name: 'message_view', methods: ['GET'])]
    public function view(int $id): Response
    {
        // Si es admin, puede ver cualquier mensaje
        if ($this->isGranted('ROLE_ADMIN')) {
            $message = $this->repository->find($id);
        } else {
            // Si es usuario normal, solo puede ver sus mensajes
            $message = $this->repository->findOneByUser($id, $this->getUser());
        }

        if (!$message) {
            throw $this->createNotFoundException('El mensaje solicitado no existe.');
        }

        return $this->render('message/view.html.twig', [
            'title' => 'Detalle de Mensaje',
            'message' => $message,
        ]);
    }

    #[Route('/delete/{id}', name: 'message_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Si es admin, puede eliminar cualquier mensaje
        if ($this->isGranted('ROLE_ADMIN')) {
            $message = $this->repository->find($id);
        } else {
            // Si es usuario normal, solo puede eliminar sus mensajes
            $message = $this->repository->findOneByUser($id, $this->getUser());
        }

        if (!$message) {
            throw $this->createNotFoundException('El mensaje solicitado no existe.');
        }

        if ($this->isCsrfTokenValid('delete_message_' . $message->getId(), $request->request->get('_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();

            $this->addFlash('success', 'Mensaje eliminado exitosamente.');
        }

        return $this->redirectToRoute('message_index');
    }
}
