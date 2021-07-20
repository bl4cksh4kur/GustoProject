<?php

namespace App\Repository;
use DateTime;
use App\Entity\BookingPlace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookingPlace|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookingPlace|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookingPlace[]    findAll()
 * @method BookingPlace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingPlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingPlace::class);
    }

    public function getPlacesFree(\DateTime $start, \DateTime $end)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT DISTINCT place.title as title, place.price as prix
                FROM App\Entity\Place place, App\Entity\BookingPlace bookingplace
                WHERE place.id NOT IN(
                    SELECT IDENTITY(bookingplaces.place) FROM App\Entity\BookingPlace bookingplaces
                    WHERE NOT (
                        bookingplaces.endDate <= :startDate
                        OR
                        bookingplaces.startDate >= :endDate
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
    //  * @return BookingPlace[] Returns an array of BookingPlace objects
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
    public function findOneBySomeField($value): ?BookingPlace
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
