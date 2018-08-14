<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 13.07.18
 * Time: 21:24
 */

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class CustomerRepository extends ServiceEntityRepository
{
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
     * @return Customer
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByLogin(string $login): Customer
    {
        return $this->qb
            ->andWhere('c.login = :login')
            ->setParameter('login', $login)
            ->getQuery()
            ->getSingleResult();
    }
}
