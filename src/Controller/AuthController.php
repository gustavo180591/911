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

        // Si el formulario se envió, manejarlo
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    // Comprobar si el usuario ya existe
                    $existingUser = $entityManager->getRepository(User::class)
                        ->findOneBy(['email' => $user->getEmail()]);

                    if ($existingUser) {
                        $this->addFlash('error', 'Este correo electrónico ya está registrado.');
                        return $this->redirectToRoute('app_register');
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

                    // Procesar las imágenes subidas
                    $fotoRostro = $form->get('fotoRostro')->getData();
                    $fotoDniFrente = $form->get('fotoDniFrente')->getData();
                    $fotoDniDorso = $form->get('fotoDniDorso')->getData();

                    // Asegurarse de que los directorios existan
                    $uploadsPhotoDir = $this->getParameter('user_photos_directory');
                    $uploadsDocDir = $this->getParameter('user_documents_directory');
                    
                    if (!file_exists($uploadsPhotoDir)) {
                        mkdir($uploadsPhotoDir, 0777, true);
                    }
                    
                    if (!file_exists($uploadsDocDir)) {
                        mkdir($uploadsDocDir, 0777, true);
                    }

                    // Rostro
                    if ($fotoRostro) {
                        $newFilename = uniqid().'.'.$fotoRostro->guessExtension();
                        $fotoRostro->move($uploadsPhotoDir, $newFilename);
                        $user->setFotoRostro($newFilename);
                        error_log("Foto de rostro guardada: $newFilename");
                    } else {
                        error_log("No se proporcionó foto de rostro");
                    }

                    // DNI frente
                    if ($fotoDniFrente) {
                        $newFilename = uniqid().'.'.$fotoDniFrente->guessExtension();
                        $fotoDniFrente->move($uploadsDocDir, $newFilename);
                        $user->setFotoDniFrente($newFilename);
                        error_log("Foto de DNI frente guardada: $newFilename");
                    } else {
                        error_log("No se proporcionó foto de DNI frente");
                    }

                    // DNI dorso
                    if ($fotoDniDorso) {
                        $newFilename = uniqid().'.'.$fotoDniDorso->guessExtension();
                        $fotoDniDorso->move($uploadsDocDir, $newFilename);
                        $user->setFotoDniDorso($newFilename);
                        error_log("Foto de DNI dorso guardada: $newFilename");
                    } else {
                        error_log("No se proporcionó foto de DNI dorso");
                    }

                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Registro exitoso! Ahora puedes iniciar sesión.');
                    // Autenticar al usuario tras el registro
                    return $this->redirectToRoute('app_login');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error durante el registro: ' . $e->getMessage());
                    return $this->redirectToRoute('app_register');
                }
            } else {
                // Si hay errores de validación, agregar un mensaje de flash
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->redirectToRoute('app_register');
            }
        }

        // Carga el JSON con los códigos de teléfono
        $jsonPath = $this->getParameter('kernel.project_dir') . '/public/json/phone-code.json';
        $jsonData = file_get_contents($jsonPath);
        $phoneCodes = json_decode($jsonData, true);

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

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $emailIntroducido = $form->get('email')->getData();
                
                // Aquí iría la lógica para:
                // 1. Verificar que el email existe.
                // 2. Generar un token único y guardarlo.
                // 3. Enviar un correo de recuperación utilizando $mailer.

                $this->addFlash('success', 'Si el email existe en nuestro sistema, te enviaremos instrucciones para restablecer la contraseña.');
                return $this->redirectToRoute('app_login');
            } else {
                // Si hay errores de validación, agregar un mensaje de flash
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->redirectToRoute('auth_forgot_password');
            }
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

        return $this->redirectToRoute('app_login');
    }
}
