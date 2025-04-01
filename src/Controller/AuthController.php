<?php

namespace App\Controller;

use App\Entity\Usuario;
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

#[Route('/auth')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'auth_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): Response {
        $user = new Usuario();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Carga el JSON con los códigos de teléfono
        $jsonPath = $this->getParameter('kernel.project_dir') . '/public/json/phone-code.json';
        $jsonData = file_get_contents($jsonPath);
        $phoneCodes = json_decode($jsonData, true);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Comprobar si el usuario ya existe
                $existingUser = $entityManager->getRepository(Usuario::class)
                    ->findOneBy(['email' => $user->getEmail()]);

                if ($existingUser) {
                    $this->addFlash('error', 'El correo electrónico ya está registrado. Intente con otro.');
                    return $this->redirectToRoute('auth_register');
                }

                // Cifrar la contraseña
                // Nota: Verifica que el nombre del campo en tu RegistrationFormType sea "Password" o ajústalo según corresponda.
                $password = password_hash($form->get('Password')->getData(), PASSWORD_BCRYPT);
                $user->setPassword($password);
                $user->setRoles(['ROLE_USER']);
                $user->setFechaRegistro(new \DateTime());

                $entityManager->persist($user);
                $entityManager->flush();

                // Autenticar al usuario tras el registro
                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'El correo electrónico ya está en uso. Intente con otro.');
                return $this->redirectToRoute('auth_register');
            }
        }

        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form->createView(),
            'phoneCodes' => $phoneCodes,
        ]);
    }

    #[Route('/login', name: 'auth_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home_index'); // Redirige al inicio si ya está autenticado
        }

        return $this->render('auth/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'auth_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Symfony maneja automáticamente el cierre de sesión
        throw new \LogicException('Este método puede estar vacío, Symfony lo gestiona automáticamente.');
    }

    #[Route('/success', name: 'auth_success', methods: ['GET'])]
    public function success(): Response
    {
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
