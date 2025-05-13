<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
    
    /**
     * Encuentra usuarios paginados por estado con filtros opcionales
     *
     * @param string $estado Estado de los usuarios (pendiente, aprobado, rechazado)
     * @param array $filters Filtros opcionales (search)
     * @param int $page Número de página
     * @param int $limit Límite de resultados por página
     * @return array Con los resultados y metadatos de paginación
     */
    public function findByEstadoPaginated(string $estado, array $filters = [], int $page = 1, int $limit = 10): array
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->where('u.estado = :estado')
            ->setParameter('estado', $estado)
            ->orderBy('u.fechaRegistro', 'DESC');
        
        // Aplicar filtros
        $this->applyFilters($queryBuilder, $filters);
        
        // Paginación
        $queryBuilder->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        
        // Usar Paginator para contar el total de resultados
        $paginator = new Paginator($queryBuilder);
        $total = count($paginator);
        
        // Calcular metadatos de paginación
        $pageCount = $limit > 0 ? ceil($total / $limit) : 1;
        
        return [
            'users' => $paginator->getIterator(),
            'pagination' => [
                'page' => $page,
                'pageCount' => max(1, $pageCount),
                'limit' => $limit,
                'total' => $total,
                'hasPrevious' => $page > 1,
                'hasNext' => $page < $pageCount,
            ]
        ];
    }
    
    /**
     * Aplica filtros a una consulta de usuarios
     *
     * @param QueryBuilder $queryBuilder
     * @param array $filters Filtros (search)
     */
    private function applyFilters(QueryBuilder $queryBuilder, array $filters): void
    {
        // Filtro de búsqueda por texto (nombre, apellido, email, DNI, etc.)
        if (!empty($filters['search'])) {
            $queryBuilder->andWhere('(
                    u.nombre LIKE :search OR
                    u.apellido LIKE :search OR
                    u.email LIKE :search OR
                    u.dni LIKE :search OR
                    u.username LIKE :search OR
                    u.telefono LIKE :search
                )')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }
    }
    
    /**
     * Cuenta la cantidad de usuarios por estado
     * 
     * @param array $filters Filtros opcionales
     * @return array Conteo de usuarios por estado
     */
    public function countByEstado(array $filters = []): array
    {
        $counts = [];
        
        foreach (['pendiente', 'aprobado', 'rechazado'] as $estado) {
            $queryBuilder = $this->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->where('u.estado = :estado')
                ->setParameter('estado', $estado);
            
            $this->applyFilters($queryBuilder, $filters);
            
            $counts[$estado] = (int) $queryBuilder->getQuery()->getSingleScalarResult();
        }
        
        return $counts;
    }
} 