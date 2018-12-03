<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 13.07.18
 * Time: 21:24
 */

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
 * @method $this resetQueryBuilder()
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
    public function findByLogin(string $login): CustomerRepository
    {
        $this->qb
            ->andWhere('c.login = :login')
            ->setParameter('login', $login);

        return $this;
    }
}
