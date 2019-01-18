<?php

namespace App\Repository;

use App\Entity\Server;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Class ServerRepository
 *
 * @package App\Repository
 */
class ServerRepository extends ServiceEntityRepository
{
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $qb;

    /**
     * ServerRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Server::class);
        $this->qb = $this->createQueryBuilder('s');
    }

    /**
     * @return Server[]
     */
    public function getServersForRegistration(): array
    {
        return $this->qb
            ->andWhere('s.registrationEnabled = true')
            ->getQuery()
            ->getResult();
    }
}
