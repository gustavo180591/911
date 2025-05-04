<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Form\ChangePasswordType; // Asegúrate de tener este formulario creado
use App\Repository\DenunciaRepository; // <-- Agrega esta línea
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/view', name: 'profile_view', methods: ['GET'])]
    public function view(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $profileForm = $this->createForm(ProfileType::class, $user);
        $profileForm->handleRequest($request);

        return $this->render('profile/view.html.twig', [
            'title' => 'Mi Perfil',
            'user' => $user,
            'profileForm' => $profileForm->createView(),
            'dni' => $user->getDni(),
        ]);
    }

    #[Route('/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $profileForm = $this->createForm(ProfileType::class, $user);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Perfil actualizado exitosamente.');

            return $this->redirectToRoute('profile_view');
        }

        return $this->render('profile/edit.html.twig', [
            'title' => 'Editar Perfil',
            'profileForm' => $profileForm->createView(),
        ]);
    }

    #[Route('/change-password', name: 'profile_change_password', methods: ['GET', 'POST'])]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Creamos el formulario de cambio de contraseña
        $changePasswordForm = $this->createForm(ChangePasswordType::class);
        $changePasswordForm->handleRequest($request);

        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
            $data = $changePasswordForm->getData();
            $currentPassword = $data['current_password'];
            $newPassword = $data['new_password'];

            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'La contraseña actual no es correcta.');
                return $this->redirectToRoute('profile_change_password');
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $entityManager->flush();

            $this->addFlash('success', 'Contraseña actualizada exitosamente.');

            return $this->redirectToRoute('profile_view');
        }

        return $this->render('profile/change_password.html.twig', [
            'title' => 'Cambiar Contraseña',
            'profileForm' => $changePasswordForm->createView(),
        ]);
    }

    #[Route('/mis-denuncias', name: 'complaint_show', methods: ['GET'])]
    public function showMyComplaints(DenunciaRepository $denunciaRepository): Response
    {
        // Si es admin, mostrar todas las denuncias
        if ($this->isGranted('ROLE_ADMIN')) {
            $denuncias = $denunciaRepository->findAll();
        } else {
            // Si es usuario normal, mostrar solo sus denuncias
            $denuncias = $denunciaRepository->findBy(['usuario' => $this->getUser()]);
        }

        return $this->render('emergency/my_complaints.html.twig', [
            'denuncias' => $denuncias,
        ]);
    }
}
