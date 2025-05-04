<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForgotPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function index(): Response
    {
        return $this->render('auth/forgot_password.html.twig');
    }
} 