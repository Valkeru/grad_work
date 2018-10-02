<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 02.10.18
 * Time: 5:27
 */

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Database;
use App\Helpers\RepositoryHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class DatabaseRepository
 *
 * @package App\Repository
 *
 * @method self strict()
 * @method self resetQueryBuilder()
 * @method Database one()
 * @method Database[] all()
 */
class DatabaseRepository extends ServiceEntityRepository
{
    use RepositoryHelper;

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $qb;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Database::class);
        $this->qb = $this->createQueryBuilder('db');
    }

    public function findById(int $id): self
    {
        $this->qb->andWhere('db.id = :id')
            ->setParameter('id', $id);

        return $this;
    }

    public function finbByCustomer(Customer $customer): self
    {
        $this->qb->andWhere('db.customer = :cistomer')
            ->setParameter('cistomer', $customer);

        return $this;
    }
}
