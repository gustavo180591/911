<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Ubicacion;
use App\Entity\EstadoDenuncia;
use App\Entity\Denuncia;

class ApiUserController extends AbstractController
{
    #[Route('/api/check-phone', name: 'api_check_phone', methods: ['POST'])]
    public function checkPhone(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['telefono'])) {
            return $this->json(['error' => 'Falta el campo telefono'], 400);
        }

        $telefono = trim($data['telefono']);
        if ($telefono === '') {
            return $this->json(['error' => 'El teléfono no puede estar vacío'], 400);
        }

        $user = $userRepository->findOneBy(['telefono' => $telefono]);
        if ($user) {
            return $this->json(['id' => $user->getId()], 200);
        } else {
            return $this->json(['error' => 'Usuario no encontrado'], 404);
        }
    }

    #[Route('/api/search-phone', name: 'api_search_phone', methods: ['POST'])]
    public function searchPhone(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['telefono'])) {
            return $this->json(['error' => 'Falta el campo telefono'], 400);
        }

        $telefono = trim($data['telefono']);
        if ($telefono === '') {
            return $this->json(['error' => 'El teléfono no puede estar vacío'], 400);
        }

        $user = $userRepository->createQueryBuilder('u')
            ->where('u.telefono LIKE :telefono')
            ->setParameter('telefono', '%' . $telefono . '%')
            ->getQuery()
            ->getOneOrNullResult();

        if ($user) {
            return $this->json(['id' => $user->getId()], 200);
        } else {
            return $this->json(['error' => 'Usuario no encontrado'], 404);
        }
    }

    #[Route('/api/create-denuncia', name: 'api_create_denuncia', methods: ['POST'])]
    public function createDenuncia(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        // Validar datos requeridos
        if (!isset($data['userId']) || !isset($data['latitude']) || !isset($data['longitude']) || !isset($data['descripcion'])) {
            return $this->json([
                'error' => 'Faltan campos requeridos: userId, latitude, longitude, descripcion'
            ], 400);
        }

        // Buscar usuario
        $user = $entityManager->getRepository(User::class)->find($data['userId']);
        if (!$user) {
            return $this->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Crear ubicación con el formato correcto de coordenadas
        $ubicacion = new Ubicacion();
        $coordenadas = $data['latitude'] . ',' . $data['longitude'];
        $ubicacion->setCoordenadas($coordenadas);
        $ubicacion->setCalle('Ubicación reportada'); // Valor por defecto
        $entityManager->persist($ubicacion);

        // Obtener estado "Pendiente"
        $estado = $entityManager->getRepository(EstadoDenuncia::class)
            ->findOneBy(['nombre' => 'Pendiente']);
        if (!$estado) {
            $estado = new EstadoDenuncia();
            $estado->setNombre('Pendiente');
            $estado->setDescripcion('Denuncia pendiente de revisión');
            $entityManager->persist($estado);
        }

        // Crear denuncia
        $denuncia = new Denuncia();
        $denuncia->setUsuario($user);
        $denuncia->setDescripcion($data['descripcion']);
        $denuncia->setFechaHora(new \DateTime());
        $denuncia->setEstado($estado);
        $denuncia->setUbicacion($ubicacion);

        $entityManager->persist($denuncia);
        $entityManager->flush();

        return $this->json([
            'id' => $denuncia->getId(),
            'message' => 'Denuncia creada exitosamente'
        ], 201);
    }
} 