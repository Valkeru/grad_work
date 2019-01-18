<?php

namespace App\ApiMapper\Internal;

use App\Entity\Mailbox;
use Valkeru\PrivateApi\Structures\Mailbox as ApiMailbox;

class MailboxMapper
{
    public static function mapMailbox(Mailbox $mailbox): ApiMailbox
    {
        $apiMailbox = new ApiMailbox;

        $apiMailbox->setName($mailbox->getName())
            ->setCustomer(CustomerMapper::mapCustomer($mailbox->getDomain()->getCustomer()))
            ->setDomainId($mailbox->getDomain()->getId())
            ->setId($mailbox->getId());

        return $apiMailbox;
    }
}
