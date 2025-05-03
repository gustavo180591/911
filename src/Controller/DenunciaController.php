<?php

namespace App\Controller;

use App\Entity\Denuncia;
use App\Entity\CategoriaDenuncia;
use App\Entity\EstadoDenuncia;
use App\Entity\Reporte;
use App\Entity\User;
use App\Form\DenunciaType;
use App\Form\ReporteType;
use App\Repository\DenunciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/emergencies')]
#[IsGranted('ROLE_USER')]
class DenunciaController extends AbstractController
{
    private DenunciaRepository $repository;

    public function __construct(DenunciaRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Listado de emergencias (index).
     */
    #[Route('/', name: 'emergency_index', methods: ['GET'])]
    public function index(): Response
    {
        // Si es admin, mostrar todas las emergencias
        if ($this->isGranted('ROLE_ADMIN')) {
            $emergencies = $this->repository->findBy([], ['fechaHora' => 'DESC']);
        } else {
            // Si es usuario normal, mostrar solo sus emergencias
            $emergencies = $this->repository->findBy(['usuario' => $this->getUser()], ['fechaHora' => 'DESC']);
        }

        return $this->render('emergency/index.html.twig', [
            'title'       => 'Listado de Emergencias',
            'emergencies' => $emergencies,
        ]);
    }

    /**
     * Crear una nueva emergencia.
     */
    #[Route('/create', name: 'emergency_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $denuncia = new Denuncia();
        $form = $this->createForm(DenunciaType::class, $denuncia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $denuncia->setUsuario($this->getUser());
            $denuncia->setFechaHora(new \DateTime());

            $entityManager->persist($denuncia);
            $entityManager->flush();

            $this->addFlash('success', 'Emergencia registrada exitosamente.');

            return $this->redirectToRoute('emergency_index');
        }

        return $this->render('emergency/create.html.twig', [
            'title' => 'Registrar Emergencia',
            'form' => $form->createView(),
        ]);
    }

    /**
     * Ver detalles de una emergencia.
     */
    #[Route('/{id}', name: 'emergency_show', methods: ['GET'])]
    public function show(Denuncia $denuncia): Response
    {
        // Verificar si el usuario tiene permiso para ver esta denuncia
        if (!$this->isGranted('ROLE_ADMIN') && $denuncia->getUsuario() !== $this->getUser()) {
            throw $this->createAccessDeniedException('No tienes permiso para ver esta emergencia.');
        }

        return $this->render('emergency/show.html.twig', [
            'title' => 'Detalle de Emergencia',
            'emergency' => $denuncia,
        ]);
    }

    /**
     * Editar una emergencia.
     */
    #[Route('/{id}/edit', name: 'emergency_edit', methods: ['GET', 'POST'])]
    public function edit(Denuncia $denuncia, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Verificar si el usuario tiene permiso para editar esta denuncia
        if (!$this->isGranted('ROLE_ADMIN') && $denuncia->getUsuario() !== $this->getUser()) {
            throw $this->createAccessDeniedException('No tienes permiso para editar esta emergencia.');
        }

        $form = $this->createForm(DenunciaType::class, $denuncia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Emergencia actualizada exitosamente.');

            return $this->redirectToRoute('emergency_index');
        }

        return $this->render('emergency/edit.html.twig', [
            'title' => 'Editar Emergencia',
            'form' => $form->createView(),
        ]);
    }

    /**
     * Eliminar una emergencia.
     */
    #[Route('/{id}', name: 'emergency_delete', methods: ['POST'])]
    public function delete(Denuncia $denuncia, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Verificar si el usuario tiene permiso para eliminar esta denuncia
        if (!$this->isGranted('ROLE_ADMIN') && $denuncia->getUsuario() !== $this->getUser()) {
            throw $this->createAccessDeniedException('No tienes permiso para eliminar esta emergencia.');
        }

        if ($this->isCsrfTokenValid(
            'delete_emergency' . $denuncia->getId(),
            $request->request->get('token')
        )) {
            $entityManager->remove($denuncia);
            $entityManager->flush();
            $this->addFlash('success', 'Emergencia eliminada exitosamente.');
        }

        return $this->redirectToRoute('emergency_index');
    }

    /**
     * Aceptar una emergencia.
     */
    #[Route('/accept/{id}', name: 'emergency_accept', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function accept(
        Denuncia $denuncia,
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response
    {
        if (!$this->isCsrfTokenValid(
            'accept_emergency_' . $denuncia->getId(),
            $request->request->get('_token')
        )) {
            $this->addFlash('error', 'Token CSRF inválido.');
            return $this->redirectToRoute('emergency_index');
        }

        $estadoAceptado = $entityManager->getRepository(EstadoDenuncia::class)
            ->findOneBy(['nombre' => 'Aceptado']);
        if (!$estadoAceptado) {
            throw new \Exception('El estado "Aceptado" no existe.');
        }

        $denuncia->setEstado($estadoAceptado);
        $entityManager->flush();

        // Enviar correo
        $email = (new Email())
            ->from('notificaciones@tuweb.com')
            ->to($denuncia->getUsuario()->getEmail())
            ->subject('Denuncia Aceptada')
            ->text('Su denuncia ha sido aceptada y será procesada.');
        $mailer->send($email);

        $this->addFlash('success', 'Emergencia aceptada exitosamente.');

        return $this->redirectToRoute('emergency_index');
    }

    /**
     * Rechazar una emergencia.
     */
    #[Route('/reject/{id}', name: 'emergency_reject', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function reject(
        Denuncia $denuncia,
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response
    {
        if (!$this->isCsrfTokenValid(
            'reject_emergency_' . $denuncia->getId(),
            $request->request->get('_token')
        )) {
            $this->addFlash('error', 'Token CSRF inválido.');
            return $this->redirectToRoute('emergency_index');
        }

        $estadoRechazado = $entityManager->getRepository(EstadoDenuncia::class)
            ->findOneBy(['nombre' => 'Rechazado']);
        if (!$estadoRechazado) {
            throw new \Exception('El estado "Rechazado" no existe.');
        }

        $denuncia->setEstado($estadoRechazado);
        $entityManager->flush();

        // Enviar correo
        $email = (new Email())
            ->from('notificaciones@tuweb.com')
            ->to($denuncia->getUsuario()->getEmail())
            ->subject('Denuncia Rechazada')
            ->text('Su denuncia ha sido rechazada. Por favor, contacte con soporte para más información.');
        $mailer->send($email);

        $this->addFlash('success', 'Emergencia rechazada exitosamente.');

        return $this->redirectToRoute('emergency_index');
    }

    /**
     * Endpoint para verificar si un usuario existe por número de teléfono.
     */
    #[Route('/check-user/{telefono}', name: 'emergency_check_user', methods: ['GET'])]
    public function checkUser(string $telefono, EntityManagerInterface $entityManager): Response
    {
        try {
            $userRepository = $entityManager->getRepository(User::class);
            $usuario = $userRepository->findOneBy(['telefono' => $telefono]);

            if (!$usuario) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Usuario no encontrado',
                    'exists' => false
                ], Response::HTTP_NOT_FOUND);
            }

            return $this->json([
                'status' => 'success',
                'message' => 'Usuario encontrado',
                'exists' => true,
                'user' => [
                    'id' => $usuario->getId(),
                    'nombre' => $usuario->getNombre(),
                    'apellido' => $usuario->getApellido(),
                    'email' => $usuario->getEmail()
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Error al buscar el usuario: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
