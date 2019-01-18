<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Helpers\RepositoryHelper;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Class CustomerRepository
 *
 * @package App\Repository
 *
 * @method $this strict()
 * @method Customer one()
 * @method Customer[] all()
 */
class CustomerRepository extends ServiceEntityRepository
{
    use RepositoryHelper;

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $qb;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
        $this->qb = $this->createQueryBuilder('c');
    }

    /**
     * @param string $login
     *
     * @return CustomerRepository
     */
    public function findByLogin(string $login): self
    {
        $this->qb
            ->andWhere('c.login = :login')
            ->setParameter('login', $login);

        return $this;
    }

    public function findById(int $id):self
    {
        $this->qb
            ->andWhere('c.id = :id')
            ->setParameter('id', $id);

        return $this;
    }
}
