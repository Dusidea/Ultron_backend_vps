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

    public function findByGameAndPeriod(string $gameName, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.gameName = :gameName')
            ->andWhere('s.collectedAt BETWEEN :from AND :to')
            ->setParameter('gameName', $gameName)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('s.collectedAt', 'ASC')
            ->addOrderBy('s.rank', 'ASC') 
            ->getQuery()
            ->getResult();
    }

    public function findDistinctGames(): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select('DISTINCT s.gameId, s.gameName')
            ->orderBy('s.gameName', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }

    
}
