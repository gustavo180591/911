<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
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

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Comprobar si el usuario ya existe
                $existingUser = $entityManager->getRepository(Usuario::class)->findOneBy(['email' => $user->getEmail()]);

                if ($existingUser) {
                    $this->addFlash('error', 'El correo electrónico ya está registrado. Intente con otro.');
                    return $this->redirectToRoute('auth_register');
                }

                // Cifrar la contraseña
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

                // ✅ Redirigir a la página de éxito o al home
                return $this->redirectToRoute('auth_success');

            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'El correo electrónico ya está en uso. Intente con otro.');
                return $this->redirectToRoute('auth_register');
            }
        }

        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'auth_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si el usuario ya está autenticado, redirigirlo
        if ($this->getUser()) {
            return $this->redirectToRoute('home_index'); // Ruta de inicio
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
    public function forgotPassword(
        Request $request,
        MailerInterface $mailer // si planeas enviar correo
    ): Response {
        if ($request->isMethod('POST')) {
            $emailIntroducido = $request->request->get('email');

            // Aquí colocarías la lógica para:
            // 1. Verificar que el email existe en la base de datos.
            // 2. Generar un token único y guardarlo (en la tabla del usuario, por ejemplo).
            // 3. Enviar un correo con el link de restablecimiento.

            // Ejemplo de envío sencillo de correo (debes configurar tus credenciales de Mailer en .env)
            /*
            $email = (new Email())
                ->from('no-reply@saci.com')
                ->to($emailIntroducido)
                ->subject('Recuperación de contraseña')
                ->text('Haz clic en el siguiente enlace para restablecer tu contraseña: [TU_LINK]');
            
            $mailer->send($email);
            */

            // Mensaje flash de confirmación (puedes personalizarlo).
            $this->addFlash('success', 'Si el email existe en nuestro sistema, te enviaremos instrucciones para restablecer la contraseña.');

            return $this->redirectToRoute('auth_login');
        }

        // Renderizar la vista con el formulario para introducir el email
        return $this->render('auth/forgot_password.html.twig');
    }
}
