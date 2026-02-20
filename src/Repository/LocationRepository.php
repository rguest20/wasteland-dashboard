<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function save(Location $location): void
    {
        $em = $this->getEntityManager();
        $em->persist($location);
        $em->flush();
    }

    public function delete(Location $location): void
    {
        $em = $this->getEntityManager();
        $em->remove($location);
        $em->flush();
    }

    public function findAllWithNpcCount(): array
    {
        return $this->createQueryBuilder('l')
            ->select('l as location, COUNT(n.id) as npcCount')
            ->leftJoin('l.npcs', 'n')
            ->groupBy('l.id')
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
