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
        } finally {
            $this->resetQueryBuilder();
        }
    }

    /**
     * @return object[]
     */
    public function all(): array
    {
        $result = $this->qb->getQuery()->getResult();
        $this->resetQueryBuilder();

        return $result;
    }

    /**
     * @return $this
     */
    public function strict(): self
    {
        $this->strict = true;

        return $this;
    }

    public function resetQueryBuilder(): self
    {
        $alias    = $this->qb->getRootAlias();
        $this->qb = $this->createQueryBuilder($alias);

        return $this;
    }
}
