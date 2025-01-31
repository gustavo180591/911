<?php

namespace App\Controller;

use App\Repository\UbicacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/buscar-ubicacion')]
class UbicacionController extends AbstractController
{
    #[Route('', name: 'buscar_ubicacion', methods: ['GET'])]
    public function buscarUbicacion(Request $request, UbicacionRepository $ubicacionRepository): JsonResponse
    {
        $direccion = $request->query->get('direccion');

        // Buscar la dirección en la base de datos
        $ubicacion = $ubicacionRepository->findOneBy(['calle' => $direccion]);

        if (!$ubicacion) {
            return new JsonResponse(['error' => 'Ubicación no encontrada'], 404);
        }

        return new JsonResponse([
            'latitud' => explode(',', $ubicacion->getCoordenadas())[0],
            'longitud' => explode(',', $ubicacion->getCoordenadas())[1]
        ]);
    }
}
