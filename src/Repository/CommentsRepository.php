<?php

namespace App\Repository;

use App\Entity\Comments;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Comments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comments[]    findAll()
 * @method Comments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comments::class);
    }

    // /**
    //  * @return Comments[] Returns an array of Comments objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comments
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getTopTen()
    {
        $query = $this->createQueryBuilder('c')
            ->select("e.id, e.title, e.startDate, e.endDate, avg(c.rating) as average")
            ->innerJoin('c.event','e')
            ->groupBy('c.event')
            ->orderBy('average', 'desc')
            ->setMaxResults(10)
        ;

       return $query->getQuery()
        ->getResult();
    }

    public function topEvent()
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.rating', 'DESC')
            ->getQuery()
            ->setMaxResults(10)
            ->getResult();
    }

    public function getMoyenne(Event $event)
    {
        $query = $this->createQueryBuilder('c')
            ->select("avg(c.rating) as average")
            ->andWhere('c.event = :event')
            ->groupBy('c.event')
            ->setParameter('event', $event);

        return $query->getQuery()
            ->getOneOrNullResult();
    }

    public function userLastRate(User $user)
    {
        return $this->createQueryBuilder('c')
            ->select('e.title, e.description, e.startDate, e.endDate, c.rating')
            ->join('c.event','e')
            ->andWhere('c.user = :user')
            ->orderBy('c.rating', 'DESC')
            ->setParameter('user',$user)
            ->getQuery()
            ->setMaxResults(5)
            ->getResult();
    }


    public function getEventRating(){
        $query= $this->createQueryBuilder('c')
            ->select("e.id")
            ->join('c.event','e')
            ->groupBy('c.event');

        return $query->getQuery()
            ->getResult();
    }

    public function getUserEventRating($user){
        $query= $this->createQueryBuilder('c')
            ->select("e.id")
            ->join('c.event','e')
            ->andWhere('c.user = :user')
            ->setParameter('user',$user)
            ->groupBy('c.event');

        return $query->getQuery()
            ->getResult();
    }

}
