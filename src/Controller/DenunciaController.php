<?php

namespace App\Controller;

use App\Entity\Denuncia;
use App\Entity\CategoriaDenuncia;
use App\Entity\EstadoDenuncia;
use App\Entity\Reporte;
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
        $emergencies = $this->repository->findBy([], ['fechaHora' => 'DESC']);

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
            // Estado por defecto = "Pendiente"
            $estadoPendiente = $entityManager->getRepository(EstadoDenuncia::class)
                ->findOneBy(['nombre' => 'Pendiente']);
            if (!$estadoPendiente) {
                throw new \Exception('El estado "Pendiente" no existe en la base de datos.');
            }
            $denuncia->setEstado($estadoPendiente);

            // Categoría por defecto = "General"
            $categoriaPorDefecto = $entityManager->getRepository(CategoriaDenuncia::class)
                ->findOneBy(['nombre' => 'General']);
            if (!$categoriaPorDefecto) {
                throw new \Exception('La categoría "General" no existe en la base de datos.');
            }
            $denuncia->setCategoria($categoriaPorDefecto);

            // Usuario actual
            $denuncia->setUsuario($this->getUser());

            // Fecha y hora actuales
            $fechaHora = new \DateTimeImmutable('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
            $denuncia->setFechaHora($fechaHora);

            // Persistir ubicación (si la hay) y la denuncia
            if ($denuncia->getUbicacion()) {
                $entityManager->persist($denuncia->getUbicacion());
            }
            $entityManager->persist($denuncia);
            $entityManager->flush();

            $this->addFlash('success', 'Emergencia creada exitosamente.');

            return $this->redirectToRoute('emergency_index');
        }

        return $this->render('emergency/create.html.twig', [
            'title' => 'Registrar Emergencia',
            'form'  => $form->createView(),
        ]);
    }

    /**
     * Ver detalle de una emergencia.
     */
    #[Route('/{id}', name: 'emergency_show', methods: ['GET'])]
public function show(Denuncia $denuncia, Request $request): Response
{
    $commentForm = null;
    if ($denuncia->getEstado() && $denuncia->getEstado()->getNombre() === 'Aceptado') {
        $report = new Reporte();
        $report->setDenuncia($denuncia);
        $commentForm = $this->createForm(ReporteType::class, $report);
        // No se hace handleRequest aquí, ya que el envío se procesará en ReportController
    }
    return $this->render('emergency/show.html.twig', [
        'title'       => 'Detalle de Emergencia',
        'emergency'   => $denuncia,
        'commentForm' => $commentForm ? $commentForm->createView() : null,
    ]);
}

    /**
     * Editar emergencia existente.
     */
    #[Route('/edit/{id}', name: 'emergency_edit', methods: ['GET', 'POST'])]
    public function edit(Denuncia $denuncia, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Si quieres restringir que solo el dueño la edite, puedes comprobarlo aquí:
        // if ($denuncia->getUsuario() !== $this->getUser()) { ... }

        $form = $this->createForm(DenunciaType::class, $denuncia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Cambios guardados exitosamente.');
            return $this->redirectToRoute('emergency_index');
        }

        return $this->render('emergency/edit.html.twig', [
            'title' => 'Editar Emergencia',
            'form'  => $form->createView(),
        ]);
    }

    /**
     * Eliminar una emergencia.
     */
    #[Route('/delete/{id}', name: 'emergency_delete', methods: ['POST'])]
    public function delete(Denuncia $denuncia, Request $request, EntityManagerInterface $entityManager): Response
    {
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
    public function accept(
        Denuncia $denuncia,
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
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
    public function reject(
        Denuncia $denuncia,
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
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
            ->text('Su denuncia ha sido rechazada. Puede comunicarse con soporte para más detalles.');
        $mailer->send($email);

        $this->addFlash('success', 'Emergencia rechazada exitosamente.');

        return $this->redirectToRoute('emergency_index');
    }

}
