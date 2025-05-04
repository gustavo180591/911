<?php

namespace App\Repository;

use App\Entity\Denuncia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Denuncia>
 */
class DenunciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Denuncia::class);
    }

    //    /**
    //     * @return Denuncia[] Returns an array of Denuncia objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Denuncia
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getDenunciasPorMes(): array
    {
        $qb = $this->createQueryBuilder('d')
            ->select('COUNT(d.id) as total, MONTH(d.fechaHora) as mes')
            ->groupBy('mes')
            ->orderBy('mes', 'ASC');

        $result = $qb->getQuery()->getResult();

        $denunciasPorMes = [];
        foreach ($result as $row) {
            $denunciasPorMes[$row['mes']] = $row['total'];
        }

        return $denunciasPorMes;
    }
}
