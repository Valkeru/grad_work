<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 13.07.18
 * Time: 21:21
 */

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class EmployeeRepository extends ServiceEntityRepository
{
    /**
     * @var QueryBuilder
     */
    private $qb;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
        $this->qb = $this->createQueryBuilder('w');
    }

    /**
     * @param string $login
     *
     * @return Employee
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByLogin(string $login): Employee
    {
        return $this->qb
            ->andWhere('w.login = :login')
            ->setParameter('login', $login)
            ->getQuery()
            ->getSingleResult();
    }
}
