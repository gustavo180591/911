<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
} 