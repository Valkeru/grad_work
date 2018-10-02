<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 02.10.18
 * Time: 14:45
 */

namespace App\Service;

use App\Entity\Domain;
use App\Entity\Mailbox;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class MailService
 *
 * @package App\Service
 */
class MailService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * MailService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Domain $domain
     * @param string $mailboxName
     *
     * @return Mailbox
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createMailbox(Domain $domain, string $mailboxName): Mailbox
    {
        $mailbox = new Mailbox();
        $mailbox->setDomain($domain)->setName($mailboxName);

        $this->entityManager->persist($mailbox);
        $this->entityManager->flush();
        $this->entityManager->refresh($mailbox);

        return $mailbox;
    }

    /**
     * @param Mailbox $mailbox
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteMailbox(Mailbox $mailbox):void
    {
        $this->entityManager->remove($mailbox);
        $this->entityManager->flush();
    }
}
