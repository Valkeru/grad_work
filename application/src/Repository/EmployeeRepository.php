<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 13.07.18
 * Time: 21:21
 */

namespace App\Repository;

use App\Entity\Employee;
use App\Helpers\RepositoryHelper;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Class EmployeeRepository
 *
 * @package App\Repository
 *
 * @method self strict()
 * @method self resetQueryBuilder()
 * @method Employee one()
 * @method Employee[] all()
 */
class EmployeeRepository extends ServiceEntityRepository
{
    use RepositoryHelper;

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
     * @param int $id
     *
     * @return $this
     */
    public function findById(int $id): self
    {
        $this->qb
            ->andWhere('w.id = :id')
            ->setParameter('id', $id);

        return $this;
    }

    /**
     * @param string $login
     *
     * @return EmployeeRepository
     */
    public function findByLogin(string $login): self
    {
        $this->qb
            ->andWhere('w.login = :login')
            ->setParameter('login', $login);

        return $this;
    }
}
