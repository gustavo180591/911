<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/admin/users/validation')]
#[IsGranted('ROLE_ADMIN')]
class UserValidationController extends AbstractController
{
    private UserRepository $repository;
    private EntityManagerInterface $entityManager;
    private const ITEMS_PER_PAGE = 6;

    public function __construct(UserRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'admin_users_validation_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Obtener parámetros de la solicitud
        $search = $request->query->get('search', '');
        $activeTab = $request->query->get('tab', 'pending');
        $page = max(1, (int)$request->query->get('page', 1));
        
        // Construir filtros
        $filters = [
            'search' => $search,
        ];
        
        // Comprobar qué estado se solicita
        $estadoMap = [
            'pending' => 'pendiente',
            'approved' => 'aprobado',
            'rejected' => 'rechazado',
        ];
        
        // Contar usuarios por estado aplicando filtros
        $counts = $this->repository->countByEstado($filters);
        
        // Resultados paginados para la pestaña activa
        $estado = $estadoMap[$activeTab] ?? 'pendiente';
        $resultados = $this->repository->findByEstadoPaginated(
            $estado,
            $filters,
            $page,
            self::ITEMS_PER_PAGE
        );
        
        // Recopilar todos los datos necesarios para la vista
        $tabData = [
            'pendiente' => [
                'users' => $activeTab === 'pending' ? $resultados['users'] : [],
                'pagination' => $activeTab === 'pending' ? $resultados['pagination'] : null,
            ],
            'aprobado' => [
                'users' => $activeTab === 'approved' ? $resultados['users'] : [],
                'pagination' => $activeTab === 'approved' ? $resultados['pagination'] : null,
            ],
            'rechazado' => [
                'users' => $activeTab === 'rejected' ? $resultados['users'] : [],
                'pagination' => $activeTab === 'rejected' ? $resultados['pagination'] : null,
            ],
        ];
        
        return $this->render('admin/validation/users.html.twig', [
            'pendingUsers' => $tabData['pendiente']['users'],
            'approvedUsers' => $tabData['aprobado']['users'],
            'rejectedUsers' => $tabData['rechazado']['users'],
            'pendingCount' => $counts['pendiente'],
            'approvedCount' => $counts['aprobado'],
            'rejectedCount' => $counts['rechazado'],
            'pagination' => $resultados['pagination'],
            'filters' => $filters,
            'activeTab' => $activeTab,
        ]);
    }

    #[Route('/{id}/approve', name: 'admin_user_approve', methods: ['POST'])]
    public function approve(User $user, Request $request, MailerInterface $mailer): Response
    {
        // Obtener parámetros para mantener el estado de filtros
        $search = $request->request->get('search', '');
        $tab = $request->request->get('tab', 'pending');
        $page = $request->request->get('page', 1);
        
        if ($this->isCsrfTokenValid('approve'.$user->getId(), $request->request->get('_token'))) {
            $user->setEstado('aprobado');
            $this->entityManager->flush();
            
            // Enviar correo de notificación
            $email = (new Email())
                ->from('sistema@911.gob.ar')
                ->to($user->getEmail())
                ->subject('Cuenta aprobada - Sistema 911')
                ->html(sprintf(
                    '<p>Estimado/a %s %s,</p>
                    <p>Nos complace informarle que su cuenta en el sistema 911 ha sido <strong>aprobada</strong>.</p>
                    <p>Ahora tiene acceso completo a todas las funcionalidades del sistema, incluyendo la creación de denuncias.</p>
                    <p>Puede iniciar sesión en: <a href="%s">%s</a></p>
                    <p>Atentamente,<br>Equipo del Sistema 911</p>',
                    $user->getNombre(),
                    $user->getApellido(),
                    $this->generateUrl('app_login', [], 0),
                    $this->generateUrl('app_login', [], 0)
                ));
            
            try {
                $mailer->send($email);
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Usuario aprobado pero no se pudo enviar el correo de notificación.');
                // Redireccionar con parámetros de filtro originales
                return $this->redirectToRoute('admin_users_validation_index', [
                    'search' => $search,
                    'tab' => $tab,
                    'page' => $page,
                ]);
            }
            
            $this->addFlash('success', 'Usuario aprobado correctamente y se ha enviado un correo de notificación.');
        }

        // Redireccionar con parámetros de filtro originales
        return $this->redirectToRoute('admin_users_validation_index', [
            'search' => $search,
            'tab' => $tab,
            'page' => $page,
        ]);
    }

    #[Route('/{id}/reject', name: 'admin_user_reject', methods: ['POST'])]
    public function reject(User $user, Request $request, MailerInterface $mailer): Response
    {
        // Obtener parámetros para mantener el estado de filtros
        $search = $request->request->get('search', '');
        $tab = $request->request->get('tab', 'pending');
        $page = $request->request->get('page', 1);
        
        if ($this->isCsrfTokenValid('reject'.$user->getId(), $request->request->get('_token'))) {
            $user->setEstado('rechazado');
            $this->entityManager->flush();
            
            // Enviar correo de notificación
            $email = (new Email())
                ->from('sistema@911.gob.ar')
                ->to($user->getEmail())
                ->subject('Cuenta rechazada - Sistema 911')
                ->html(sprintf(
                    '<p>Estimado/a %s %s,</p>
                    <p>Lamentamos informarle que su cuenta en el sistema 911 ha sido <strong>rechazada</strong>.</p>
                    <p>Por favor, contacte al administrador del sistema para obtener más información.</p>
                    <p>Atentamente,<br>Equipo del Sistema 911</p>',
                    $user->getNombre(),
                    $user->getApellido()
                ));
            
            try {
                $mailer->send($email);
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Usuario rechazado pero no se pudo enviar el correo de notificación.');
                // Redireccionar con parámetros de filtro originales
                return $this->redirectToRoute('admin_users_validation_index', [
                    'search' => $search,
                    'tab' => $tab,
                    'page' => $page,
                ]);
            }
            
            $this->addFlash('success', 'Usuario rechazado correctamente y se ha enviado un correo de notificación.');
        }

        // Redireccionar con parámetros de filtro originales
        return $this->redirectToRoute('admin_users_validation_index', [
            'search' => $search,
            'tab' => $tab,
            'page' => $page,
        ]);
    }
} 