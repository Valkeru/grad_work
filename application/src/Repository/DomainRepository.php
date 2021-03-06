<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Domain;
use App\Helpers\RepositoryHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class DomainRepository
 *
 * @package App\Repository
 *
 * @method Domain|NULL one()
 * @method Domain[] all()
 * @method self strict()
 */
class DomainRepository extends ServiceEntityRepository
{
    use RepositoryHelper;

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $qb;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Domain::class);
        $this->qb = $this->createQueryBuilder('d');
    }

    /**
     * @param Customer $customer
     *
     * @return DomainRepository
     */
    public function findByCustomer(Customer $customer): self
    {
        $this->qb->andWhere('d.customer = :customer')
            ->setParameter('customer', $customer);

        return $this;
    }

    /**
     * @param int $id
     *
     * @return DomainRepository
     */
    public function findById(int $id): self
    {
        $this->qb->andWhere('d.id = :id')
            ->setParameter('id', $id);

        return $this;
    }
}
