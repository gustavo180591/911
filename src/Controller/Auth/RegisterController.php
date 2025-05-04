<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verificar si el usuario ya existe
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);
            if ($existingUser) {
                $this->addFlash('error', 'El nombre de usuario ya está en uso');
                return $this->render('auth/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'phoneCodes' => $this->getPhoneCodes(),
                ]);
            }

            // Verificar si el email ya existe
            $existingEmail = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if ($existingEmail) {
                $this->addFlash('error', 'El email ya está en uso');
                return $this->render('auth/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'phoneCodes' => $this->getPhoneCodes(),
                ]);
            }

            // Codificar la contraseña
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Guardar el código de país del teléfono
            $countryCode = $request->request->get('countryCode');
            $telefono = $countryCode . $form->get('telefono')->getData();
            $user->setTelefono($telefono);

            // La fecha de registro se establece automáticamente en el constructor de User
            // No es necesario establecerla manualmente

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', '¡Registro exitoso! Ahora puedes iniciar sesión.');
            return $this->redirectToRoute('auth_success');
        }

        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form->createView(),
            'phoneCodes' => $this->getPhoneCodes(),
        ]);
    }

    private function getPhoneCodes(): array
    {
        return [
            ['name' => 'Argentina', 'phoneCode' => '+54', 'flagEmoji' => '🇦🇷'],
            ['name' => 'Brasil', 'phoneCode' => '+55', 'flagEmoji' => '🇧🇷'],
            ['name' => 'Chile', 'phoneCode' => '+56', 'flagEmoji' => '🇨🇱'],
            ['name' => 'Colombia', 'phoneCode' => '+57', 'flagEmoji' => '🇨🇴'],
            ['name' => 'Ecuador', 'phoneCode' => '+593', 'flagEmoji' => '🇪🇨'],
            ['name' => 'México', 'phoneCode' => '+52', 'flagEmoji' => '🇲🇽'],
            ['name' => 'Perú', 'phoneCode' => '+51', 'flagEmoji' => '🇵🇪'],
            ['name' => 'Uruguay', 'phoneCode' => '+598', 'flagEmoji' => '🇺🇾'],
            ['name' => 'Venezuela', 'phoneCode' => '+58', 'flagEmoji' => '🇻🇪'],
        ];
    }
} 