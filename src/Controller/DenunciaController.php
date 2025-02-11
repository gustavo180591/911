<?php

namespace App\Controller;

use App\Entity\Denuncia;
use App\Entity\Ubicacion;
use App\Entity\CategoriaDenuncia;
use App\Entity\EstadoDenuncia;
use App\Form\DenunciaType;
use App\Repository\DenunciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/emergencies')]
#[IsGranted('ROLE_USER')]
class DenunciaController extends AbstractController
{
    private DenunciaRepository $repository;

    public function __construct(DenunciaRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/', name: 'emergency_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $estado = $request->query->get('estado');
        $criteria = ['usuario' => $this->getUser()];
        
        if ($estado) {
            $criteria['estado'] = $estado;
        }

        $emergencies = $this->repository->findBy($criteria, ['fechaCreacion' => 'DESC']);

        return $this->render('emergency/index.html.twig', [
            'title' => 'Mis Emergencias',
            'emergencies' => $emergencies,
        ]);
    }

    #[Route('/create', name: 'emergency_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $emergency = new Denuncia();
        $form = $this->createForm(DenunciaType::class, $emergency);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $estadoPendiente = $entityManager->getRepository(EstadoDenuncia::class)->findOneBy(['nombre' => 'Pendiente']);
            if (!$estadoPendiente) {
                throw new \Exception('El estado "Pendiente" no existe en la base de datos.');
            }
            $emergency->setEstado($estadoPendiente);

            $categoriaPorDefecto = $entityManager->getRepository(CategoriaDenuncia::class)->findOneBy(['nombre' => 'General']);
            if (!$categoriaPorDefecto) {
                throw new \Exception('La categoría "General" no existe en la base de datos.');
            }
            $emergency->setCategoria($categoriaPorDefecto);

            // Asignar datos del denunciante
            $emergency->setUsuario($this->getUser());

            // Asignar la fecha de creación
            $fechaCreacion = new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
            $emergency->setFechaCreacion($fechaCreacion);

            
            $entityManager->persist($emergency);
            $entityManager->flush();

            $this->addFlash('success', 'Emergencia creada exitosamente.');
            return $this->redirectToRoute('emergency_index');
        }
    
        return $this->render('emergency/create.html.twig', [
            'title' => 'Registrar Emergencia',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/accept/{id}', name: 'emergency_accept', methods: ['POST'])]
    public function accept(int $id, Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $emergency = $this->repository->find($id);
        if (!$emergency) {
            throw $this->createNotFoundException('La emergencia solicitada no existe.');
        }

        if ($this->isCsrfTokenValid('accept_emergency_' . $emergency->getId(), $request->request->get('_token'))) {
            $estadoAceptado = $entityManager->getRepository(EstadoDenuncia::class)->findOneBy(['nombre' => 'Aceptado']);
            if (!$estadoAceptado) {
                throw new \Exception('El estado "Aceptado" no existe.');
            }

            $emergency->setEstado($estadoAceptado);
            $entityManager->flush();

            // Enviar correo al denunciante
            $email = (new Email())
                ->from('notificaciones@tuweb.com')
                ->to($emergency->getUsuario()->getEmail())
                ->subject('Denuncia Aceptada')
                ->text('Su denuncia ha sido aceptada y será procesada.');
            
            $mailer->send($email);

            $this->addFlash('success', 'Emergencia aceptada exitosamente.');
        }

        return $this->redirectToRoute('emergency_view', ['id' => $id]);
    }

    #[Route('/reject/{id}', name: 'emergency_reject', methods: ['POST'])]
    public function reject(int $id, Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $emergency = $this->repository->find($id);
        if (!$emergency) {
            throw $this->createNotFoundException('La emergencia solicitada no existe.');
        }

        if ($this->isCsrfTokenValid('reject_emergency_' . $emergency->getId(), $request->request->get('_token'))) {
            $estadoRechazado = $entityManager->getRepository(EstadoDenuncia::class)->findOneBy(['nombre' => 'Rechazado']);
            if (!$estadoRechazado) {
                throw new \Exception('El estado "Rechazado" no existe.');
            }

            $emergency->setEstado($estadoRechazado);
            $entityManager->flush();

            // Enviar correo al denunciante
            $email = (new Email())
                ->from('notificaciones@tuweb.com')
                ->to($emergency->getUsuario()->getEmail())
                ->subject('Denuncia Rechazada')
                ->text('Su denuncia ha sido rechazada. Puede comunicarse con soporte para más detalles.');
            
            $mailer->send($email);

            $this->addFlash('success', 'Emergencia rechazada exitosamente.');
        }

        return $this->redirectToRoute('emergency_view', ['id' => $id]);
    }
}
