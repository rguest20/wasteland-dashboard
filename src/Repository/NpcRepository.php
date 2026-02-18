<?php

namespace App\Repository;

use App\Entity\Npc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Npc>
 */
class NpcRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Npc::class);
    }

    public function findAllWithRole(): array
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.role', 'r')
            ->addSelect('r')
            ->getQuery()
            ->getResult();
    }

    public function save(Npc $npc): void
    {
        $em = $this->getEntityManager();
        $em->persist($npc);
        $em->flush();
    }

    public function findOneWithRole(int $id): ?Npc
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.role', 'r')
            ->addSelect('r')
            ->andWhere('n.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Npc[] Returns an array of Npc objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Npc
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
