<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getEventByDate($now){
         $query = $this->createQueryBuilder('e')
            ->andWhere('e.endDate >= :now')
            ->setParameter('now',$now)
            ->orderBy('e.startDate', 'ASC');
        return $query->getQuery()
            ->getResult();
    }

    public function getEventByTitle($title, $now){
        $query= $this->createQueryBuilder('e')
            ->andWhere('e.endDate >= :now')
            ->andWhere('e.title LIKE :title')
            ->setParameter('title','%'.$title.'%')
            ->setParameter('now',$now)
            ->orderBy('e.startDate', 'ASC');

        return $query->getQuery()
            ->getResult();
    }
}
