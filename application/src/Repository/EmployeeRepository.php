<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 13.07.18
 * Time: 21:21
 */

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\QueryBuilder;

class EmployeeRepository extends EntityRepository
{
    /**
     * @var QueryBuilder
     */
    private $qb;

    public function __construct(EntityManagerInterface $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);

        $this->qb = $this->createQueryBuilder('w');
    }


}
