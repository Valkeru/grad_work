<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 02.10.18
 * Time: 5:35
 */

namespace App\Repository;

use App\Entity\Database;
use App\Entity\DatabaseAccess;
use App\Helpers\RepositoryHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class DatabaseAccessRepository
 *
 * @package App\Repository
 *
 * @method self strict()
 * @method self resetQueryBuilder()
 * @method DatabaseAccess one()
 * @method DatabaseAccess[] all()
 */
class DatabaseAccessRepository extends ServiceEntityRepository
{
    use RepositoryHelper;

    private $qb;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatabaseAccess::class);
        $this->qb = $this->createQueryBuilder('da');
    }

    public function findById(int $id): self
    {
        $this->qb->andWhere('da.id = :id')
            ->setParameter('id', $id);

        return $this;
    }

    public function findByDatabase(Database $database): self
    {
        $this->qb->andWhere('da.database = :database')
            ->setParameter('database', $database);

        return $this;
    }

    public function findByHost(string $host): self
    {
        $this->qb->andWhere('da.host = :host')
            ->setParameter('host', $host);

        return $this;
    }
}
