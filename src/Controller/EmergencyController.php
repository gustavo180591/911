<?php

namespace App\Controller;

use App\Entity\Denuncia;
use App\Entity\Ubicacion;
use App\Entity\EstadoDenuncia;
use App\Form\DenunciaType;
use App\Repository\DenunciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/emergencies')]
#[IsGranted('ROLE_USER')]
class EmergencyController extends AbstractController
{
    private DenunciaRepository $repository;

    public function __construct(DenunciaRepository $repository)
    {
        $this->repository = $repository;
    }




    #[Route('/', name: 'emergency_index', methods: ['GET'])]
    public function index(): Response
    {
        // Obtener todas las emergencias creadas por el usuario autenticado
        $emergencies = $this->repository->findBy(['usuario' => $this->getUser()], ['fechaCreacion' => 'DESC']);

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
        $direccion = $form->get('direccion')->getData();
        $ubicacion = $entityManager->getRepository(Ubicacion::class)->findOneBy(['calle' => $direccion]);

        if (!$ubicacion) {
            $ubicacion = new Ubicacion();
            $ubicacion->setCalle($direccion);
            $ubicacion->setNumero(''); // Ajusta si es necesario
            $ubicacion->setCoordenadas(''); // Ajusta si es necesario
            $entityManager->persist($ubicacion);
        }

        $emergency->setUbicacion($ubicacion);

        // Obtener el estado "Iniciado" desde la base de datos
        $estadoIniciado = $entityManager->getRepository(EstadoDenuncia::class)->findOneBy(['nombre' => 'Iniciado']);

        if (!$estadoIniciado) {
            throw new \Exception('El estado "Iniciado" no existe en la base de datos.');
        }

        $emergency->setEstado($estadoIniciado);
        $emergency->setUsuario($this->getUser());
        $emergency->setFechaCreacion(new \DateTime());

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

    #[Route('/view/{id}', name: 'emergency_view', methods: ['GET'])]
    public function view(int $id): Response
    {
        // Obtener una emergencia específica del usuario autenticado
        $emergency = $this->repository->findOneBy(['id' => $id, 'usuario' => $this->getUser()]);

        if (!$emergency) {
            throw $this->createNotFoundException('La emergencia solicitada no existe.');
        }

        return $this->render('emergency/view.html.twig', [
            'title' => 'Detalle de Emergencia',
            'emergency' => $emergency,
        ]);
    }

    #[Route('/edit/{id}', name: 'emergency_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $emergency = $this->repository->findOneBy(['id' => $id, 'usuario' => $this->getUser()]);

        if (!$emergency) {
            throw $this->createNotFoundException('La emergencia solicitada no existe.');
        }

        $form = $this->createForm(DenunciaType::class, $emergency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $direccion = $form->get('direccion')->getData();
            $ubicacion = $entityManager->getRepository(Ubicacion::class)->findOneBy(['calle' => $direccion]);

            if (!$ubicacion) {
                $ubicacion = new Ubicacion();
                $ubicacion->setCalle($direccion);
                $ubicacion->setNumero('');
                $ubicacion->setCoordenadas('');
                $entityManager->persist($ubicacion);
            }

            $emergency->setUbicacion($ubicacion);
            $entityManager->flush();

            $this->addFlash('success', 'Emergencia actualizada exitosamente.');

            return $this->redirectToRoute('emergency_index');
        }

        return $this->render('emergency/edit.html.twig', [
            'title' => 'Editar Emergencia',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'emergency_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $emergency = $this->repository->findOneBy(['id' => $id, 'usuario' => $this->getUser()]);

        if (!$emergency) {
            throw $this->createNotFoundException('La emergencia solicitada no existe.');
        }

        if ($this->isCsrfTokenValid('delete_emergency_' . $emergency->getId(), $request->request->get('_token'))) {
            $entityManager->remove($emergency);
            $entityManager->flush();

            $this->addFlash('success', 'Emergencia eliminada exitosamente.');
        }

        return $this->redirectToRoute('emergency_index');
    }
}