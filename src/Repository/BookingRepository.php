<?php

namespace App\Repository;

use App\Entity\Booking;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function getSpacesFree(\DateTime $start, \DateTime $end)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT DISTINCT space.title as title, space.nbplace as nombreDePlace, space.type as typeSpace, space.price as prix, space.adress as adress, space.illustration as illustration
                FROM App\Entity\Space space, App\Entity\Booking booking
                WHERE space.id NOT IN(
                    SELECT IDENTITY(bookings.space) FROM App\Entity\Booking bookings
                    WHERE NOT (
                        bookings.endDate <= :startDate
                        OR
                        bookings.startDate >= :endDate
                        )
                    )
                '
            )->setParameters([
                'startDate' => $start,
                'endDate' => $end
            ])
            ->getResult();

    }

    // /**
    //  * @return Booking[] Returns an array of Booking objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Booking
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
