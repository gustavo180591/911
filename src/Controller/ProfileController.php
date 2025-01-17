<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\ProfileType;
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
    #[Route('/', name: 'profile_view', methods: ['GET'])]
    public function view(): Response
    {
        return $this->render('profile/view.html.twig', [
            'title' => 'Mi Perfil',
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Usuario $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Perfil actualizado exitosamente.');

            return $this->redirectToRoute('profile_view');
        }

        return $this->render('profile/edit.html.twig', [
            'title' => 'Editar Perfil',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/change-password', name: 'profile_change_password', methods: ['GET', 'POST'])]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var Usuario $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $currentPassword = $request->request->get('current_password');
            $newPassword = $request->request->get('new_password');

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
        ]);
    }
}
