<?php

namespace App\Repository;

use App\Entity\Streams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Streams>
 */
class StreamsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Streams::class);
    }

    #this method retrieves streams within a category bewteen 2 dates
    public function findByGameAndPeriod(string $gameName, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        // transform into DateTimeImmutable if necessary, otherwise use like it is
        $fromImmutable = ($from instanceof \DateTimeImmutable) ? $from : new \DateTimeImmutable($from->format('Y-m-d H:i:s'));
        $toImmutable   = ($to instanceof \DateTimeImmutable) ? $to : new \DateTimeImmutable($to->format('Y-m-d H:i:s'));

        // Semi-open interval
        $fromImmutable = $fromImmutable->setTime(0, 0, 0);
        $toImmutable   = $toImmutable->modify('+1 day')->setTime(0, 0, 0);

        return $this->createQueryBuilder('s')
            ->where('s.gameName = :gameName')
            ->andWhere('s.collectedAt >= :from')
            ->andWhere('s.collectedAt < :to')
            ->andWhere('s.rank < 26')
            ->setParameter('gameName', $gameName)
            ->setParameter('from', $fromImmutable)
            ->setParameter('to', $toImmutable)
            ->orderBy('s.collectedAt', 'ASC')
            ->addOrderBy('s.rank', 'ASC')
            ->getQuery()
            ->getResult();
    }

    #this method retrieves streams at given timestamp (date+ hour + minute) within a category
    public function findByGameAndHour(string $gameName, \DateTimeInterface $atTimeStamp): array
    { 
        // Début de la minute (secondes à 0)
        $startOfMinute = (clone $atTimeStamp)->setTime(
            (int) $atTimeStamp->format('H'),
            (int) $atTimeStamp->format('i'),
            0
        );

        // Fin de la minute (secondes à 59)
        $endOfMinute = (clone $startOfMinute)->modify('+59 seconds');

        return $this->createQueryBuilder('s')
            ->where('s.gameName = :gameName')
            ->andWhere('s.collectedAt BETWEEN :start AND :end')
            ->andWhere('s.rank < 26')
            ->setParameter('gameName', $gameName)
            ->setParameter('start', $startOfMinute)
            ->setParameter('end', $endOfMinute)
            ->orderBy('s.collectedAt', 'ASC')
            ->addOrderBy('s.rank', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //this method retrieves the list of categories available within the DataBase
    public function findDistinctGames(): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s.gameId AS gameId, MAX(s.gameName) AS gameName')
            ->where('s.gameId IS NOT NULL')
            ->groupBy('s.gameId')
            ->orderBy('gameName', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }
}
