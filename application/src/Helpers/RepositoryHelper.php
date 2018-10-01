<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 17.09.18
 * Time: 10:41
 */

namespace App\Helpers;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Trait RepositoryHelper
 *
 * @package App\Helpers
 * @property QueryBuilder $qb
 */
trait RepositoryHelper
{
    private $strict = false;

    /**
     * @return object|NULL
     * @throws NotFoundHttpException
     * @throws NonUniqueResultException
     */
    public function one(): ?object
    {
        try {
            return $this->qb->getQuery()->getSingleResult();
        } catch (NoResultException $exception) {
            if (!$this->strict) {
                return NULL;
            }

            throw new NotFoundHttpException();
        }
    }

    /**
     * @return object[]
     */
    public function all(): array
    {
        return $this->qb->getQuery()->getResult();
    }

    /**
     * @return $this
     */
    public function strict()
    {
        $this->strict = true;

        return $this;
    }
}
