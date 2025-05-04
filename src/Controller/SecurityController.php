<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormFactoryInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, FormFactoryInterface $formFactory): Response
    {
        // Si el usuario ya está logueado, redirigir a la página principal
        if ($this->getUser()) {
            return $this->redirectToRoute('home_index');
        }

        // Obtener el error de login si hay
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Último nombre de usuario ingresado
        $lastUsername = $authenticationUtils->getLastUsername();

        // Crear el formulario de login
        $form = $formFactory->createNamedBuilder('', 'Symfony\Component\Form\Extension\Core\Type\FormType')
            ->add('_username', TextType::class, [
                'label' => 'Email, Teléfono, DNI o Nombre de Usuario',
                'attr' => [
                    'placeholder' => 'ejemplo@hotmail.com, +54123456789, 12345678 o usuario123',
                    'autofocus' => true
                ],
                'data' => $lastUsername
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'Contraseña',
                'attr' => [
                    'placeholder' => '••••••'
                ]
            ])
            ->getForm();

        return $this->render('auth/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Este método puede estar vacío, Symfony lo gestiona automáticamente
        throw new \Exception('No se debe llegar a este método');
    }
}
