<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Domain;
use App\Entity\Mailbox;
use App\Helpers\RepositoryHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class MailboxRepository
 *
 * @package App\Repository
 *
 * @method self strict()
 * @method Mailbox one()
 * @method Mailbox[] all()
 */
class MailboxRepository extends ServiceEntityRepository
{
    use RepositoryHelper;

    private $qb;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mailbox::class);
        $this->qb = $this->createQueryBuilder('m');
    }

    public function findById(int $id): self
    {
        $this->qb->andWhere('m.id = :id')
            ->setParameter('id', $id);

        return $this;
    }

    public function findByCustomer(Customer $customer): self
    {
        $this->qb->join('m.domain', 'd')
            ->andWhere('d.customer = :customer')
            ->setParameter('customer', $customer);

        return $this;
    }

    public function findByDomain(Domain $domain)
    {
        $this->qb->andWhere('m.domain = :domain')
            ->setParameter('domain', $domain);

        return $this;
    }
}
