<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserManagementController extends AbstractController
{
    #[Route('/user/management', name: 'app_user_management')]
    public function index(): Response
    {
        return $this->render('user_management/index.html.twig', [
            'controller_name' => 'UserManagementController',
        ]);
    }
}
