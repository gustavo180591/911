<?php

namespace App\Controller;

use App\Entity\Denuncia;
use App\Entity\CategoriaDenuncia;
use App\Entity\EstadoDenuncia;
use App\Entity\Reporte;
use App\Entity\User;
use App\Entity\Mensaje;
use App\Form\DenunciaType;
use App\Form\ReporteType;
use App\Repository\DenunciaRepository;
use App\Repository\ReporteRepository;
use App\Repository\MensajeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\Mapping\Column;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\Collection;

#[Route('/emergencies')]
#[IsGranted('ROLE_USER')]
class DenunciaController extends AbstractController
{
    private DenunciaRepository $repository;
    private ReporteRepository $reporteRepository;
    private MensajeRepository $mensajeRepository;

    public function __construct(DenunciaRepository $repository, ReporteRepository $reporteRepository, MensajeRepository $mensajeRepository)
    {
        $this->repository = $repository;
        $this->reporteRepository = $reporteRepository;
        $this->mensajeRepository = $mensajeRepository;
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
            // Obtener el estado "Pendiente" o crearlo si no existe
            $estado = $entityManager->getRepository(EstadoDenuncia::class)->findOneBy(['nombre' => 'Pendiente']);
            if (!$estado) {
                $estado = new EstadoDenuncia();
                $estado->setNombre('Pendiente');
                $estado->setDescripcion('Denuncia pendiente de revisión');
                $entityManager->persist($estado);
            }

            $denuncia->setUsuario($this->getUser());
            $denuncia->setFechaHora(new \DateTime());
            $denuncia->setEstado($estado);

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
    public function show(Denuncia $denuncia, Request $request): Response
    {
        // Verificar si el usuario tiene permiso para ver esta denuncia
        if (!$this->isGranted('ROLE_ADMIN') && $denuncia->getUsuario() !== $this->getUser()) {
            throw $this->createAccessDeniedException('No tienes permiso para ver esta emergencia.');
        }

        // Obtener todos los comentarios de la denuncia
        $comentarios = $this->reporteRepository->findBy(['denuncia' => $denuncia], ['fechaHora' => 'ASC']);

        // Crear el formulario de comentario solo si la emergencia está aceptada
        $commentForm = null;
        if ($denuncia->getEstado() && $denuncia->getEstado()->getNombre() === 'Aceptado') {
            $reporte = new Reporte();
            $reporte->setDenuncia($denuncia);
            $reporte->setUsuario($this->getUser());
            $commentForm = $this->createForm(ReporteType::class, $reporte);
        }

        // Obtener observaciones (mensajes) asociados a la denuncia
        $observaciones = $this->mensajeRepository->findBy([
            'denuncia' => $denuncia
        ], ['fechaEnvio' => 'ASC']);

        return $this->render('emergency/show.html.twig', [
            'title' => 'Detalle de Emergencia',
            'emergency' => $denuncia,
            'commentForm' => $commentForm ? $commentForm->createView() : null,
            'comentarios' => $comentarios,
            'observaciones' => $observaciones,
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
        EntityManagerInterface $entityManager
        
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
/*         $email = (new Email())
            ->from('notificaciones@tuweb.com')
            ->to($denuncia->getUsuario()->getEmail())
            ->subject('Denuncia Aceptada')
            ->text('Su denuncia ha sido aceptada y será procesada.');
        $mailer->send($email); */

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
        EntityManagerInterface $entityManager
       
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
/*         $email = (new Email())
            ->from('notificaciones@tuweb.com')
            ->to($denuncia->getUsuario()->getEmail())
            ->subject('Denuncia Rechazada')
            ->text('Su denuncia ha sido rechazada. Por favor, contacte con soporte para más información.');
        $mailer->send($email); */

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

    /**
     * Crear un nuevo comentario en la emergencia.
     */
    #[Route('/{id}/comment', name: 'emergency_comment', methods: ['POST'])]
    public function addComment(
        Denuncia $denuncia,
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        if (!$denuncia->getEstado() || $denuncia->getEstado()->getNombre() !== 'Aceptado') {
            throw $this->createAccessDeniedException('No se pueden agregar comentarios en esta emergencia.');
        }

        $reporte = new Reporte();
        $reporte->setDenuncia($denuncia);
        $reporte->setUsuario($this->getUser());
        $reporte->setFechaHora(new \DateTime());

        $form = $this->createForm(ReporteType::class, $reporte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reporte);
            $entityManager->flush();

            // Enviar notificación por email al otro usuario
            $destinatario = $this->getUser() === $denuncia->getUsuario() 
                ? $denuncia->getUsuario() 
                : $this->getUser();

            $email = (new Email())
                ->from('notificaciones@tuweb.com')
                ->to($destinatario->getEmail())
                ->subject('Nuevo comentario en tu emergencia')
                ->html(sprintf(
                    '<p>Has recibido un nuevo comentario en tu emergencia:</p>
                    <p><strong>%s</strong></p>
                    <p>Para ver el comentario, accede a: <a href="%s">%s</a></p>',
                    $reporte->getDescripcion(),
                    $this->generateUrl('emergency_show', ['id' => $denuncia->getId()]),
                    $this->generateUrl('emergency_show', ['id' => $denuncia->getId()])
                ));

            $mailer->send($email);

            $this->addFlash('success', 'Comentario agregado exitosamente.');
        }

        return $this->redirectToRoute('emergency_show', ['id' => $denuncia->getId()]);
    }

    #[Route('/{id}/observacion', name: 'mensaje_observacion', methods: ['POST'])]
    public function observacion(Denuncia $denuncia, Request $request, EntityManagerInterface $em): Response
    {
        $observacion = $request->request->get('observacion');
        if ($observacion) {
            $mensaje = new Mensaje();
            $mensaje->setDenuncia($denuncia);
            $mensaje->setContenido($observacion);
            $mensaje->setFechaEnvio(new \DateTime());
            $mensaje->setUsuarioRemitente($this->getUser());
            $mensaje->setUsuarioDestinatario($denuncia->getUsuario());
            $em->persist($mensaje);
            $em->flush();
            $this->addFlash('success', 'Observación guardada correctamente.');
        }
        return $this->redirectToRoute('emergency_show', ['id' => $denuncia->getId()]);
    }
}
