<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\ForgotPasswordFormType; // Corrección: Importar desde App\Form
use App\Security\LoginFormAuthenticator;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/auth')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Carga el JSON con los códigos de teléfono
        $jsonPath = $this->getParameter('kernel.project_dir') . '/public/json/phone-code.json';
        $jsonData = file_get_contents($jsonPath);
        $phoneCodes = json_decode($jsonData, true);

        if ($form->isSubmitted() && $form->isValid()) {
            // Comprobar si el usuario ya existe
            $existingUser = $entityManager->getRepository(User::class)
                ->findOneBy(['email' => $user->getEmail()]);

            if ($existingUser) {
                $this->addFlash('error', 'Este correo electrónico ya está registrado.');
                return $this->render('auth/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            // Codificar la contraseña
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Asignar el rol ROLE_USER por defecto
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            // Autenticar al usuario tras el registro
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form->createView(),
            'phoneCodes' => $phoneCodes,
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // El controlador puede estar vacío - será interceptado por la clave de logout de seguridad
        throw new \LogicException('Este método puede estar vacío - será interceptado por la clave de logout de seguridad');
    }

    #[Route('/auth/success', name: 'auth_success')]
    public function success()
    {
        // Lógica de la página de éxito
        return $this->render('auth/success.html.twig');
    }

    #[Route('/forgot-password', name: 'auth_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request, MailerInterface $mailer): Response
    {
        // Crea el formulario utilizando ForgotPasswordFormType
        $form = $this->createForm(ForgotPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emailIntroducido = $form->get('email')->getData();
            
            // Aquí iría la lógica para:
            // 1. Verificar que el email existe.
            // 2. Generar un token único y guardarlo.
            // 3. Enviar un correo de recuperación utilizando $mailer.

            $this->addFlash('success', 'Si el email existe en nuestro sistema, te enviaremos instrucciones para restablecer la contraseña.');
            return $this->redirectToRoute('auth_login');
        }

        return $this->render('auth/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/delete-account', name: 'account_delete_success', methods: ['GET'])]
    public function deleteAccount(Request $request): Response
    {
        if ($this->isGranted('ROLE_USER')) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Tu cuenta ha sido eliminada con éxito.');
            return $this->redirectToRoute('home_index');
        }

        return $this->redirectToRoute('auth_login');
    }
}
