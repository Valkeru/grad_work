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
    /**
     * @var EntityManager
     */
    private static $em;

    public function setEm(EntityManager $em): void
    {
        if (self::$em === NULL) {
            self::$em = $em;
        }
    }

    public static function find(): EntityRepository
    {
        return self::$em->getRepository(self::class);
    }
}
