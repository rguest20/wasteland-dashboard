<?php

namespace App\Repository;

use App\Entity\WorldSecret;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorldSecret>
 */
class WorldSecretRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorldSecret::class);
    }

    public function save(WorldSecret $worldSecret): void
    {
        $em = $this->getEntityManager();
        $em->persist($worldSecret);
        $em->flush();
    }

    public function delete(WorldSecret $worldSecret): void
    {
        $em = $this->getEntityManager();
        $em->remove($worldSecret);
        $em->flush();
    }

    //    /**
    //     * @return WorldSecret[] Returns an array of WorldSecret objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?WorldSecret
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
