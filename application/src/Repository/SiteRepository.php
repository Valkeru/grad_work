<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Site;
use App\Helpers\RepositoryHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class SiteRepository
 *
 * @package App\Repository
 *
 * @method Site one()
 * @method Site[] all()
 * @method self strict()
 */
class SiteRepository extends ServiceEntityRepository
{
    use RepositoryHelper;

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $qb;

    /**
     * SiteRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Site::class);
        $this->qb = $this->createQueryBuilder('s');
    }

    /**
     * @param Customer $customer
     *
     * @return SiteRepository
     */
    public function findByCustomer(Customer $customer): self
    {
        $this->qb->andWhere('s.customer = :customer')
            ->setParameter('customer', $customer);

        return $this;
    }

    /**
     * @param int $id
     *
     * @return SiteRepository
     */
    public function findById(int $id): self
    {
        $this->qb->andWhere('s.id = :id')
            ->setParameter('id', $id);

        return $this;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function findByPath(string $path): self
    {
        $this->qb->andWhere('s.path = :path')
            ->setParameter('path', $path);

        return $this;
    }
}
