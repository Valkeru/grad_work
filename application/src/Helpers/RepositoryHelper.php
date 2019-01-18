<?php

namespace App\Helpers;

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
     */
    public function one(): ?object
    {
        try {
            $result = $this->qb->getQuery()->getResult();

            if ($result === [] || $result === NULL) {
                if ($this->strict) {
                    throw new NotFoundHttpException();
                }

                return NULL;
            }

            if (\is_array($result)) {
                return $result[0];
            }

            return $result;

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

    private function resetQueryBuilder(): void
    {
        $alias    = $this->qb->getRootAliases()[0];
        $this->qb = $this->createQueryBuilder($alias);
    }
}
