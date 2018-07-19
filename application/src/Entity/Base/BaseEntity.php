<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 02.06.18
 * Time: 14:04
 */

namespace App\Entity\Base;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

abstract class BaseEntity
{
    public static function find(EntityManager $em): EntityRepository
    {
        return $em->getRepository(self::class);
    }
}
