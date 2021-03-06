<?php

namespace App\ApiMapper;

use App\Entity\Mailbox;
use Valkeru\PublicApi\Structures\Mailbox as ApiMailbox;

class MailboxMapper
{
    public static function mapMailbox(Mailbox $mailbox): ApiMailbox
    {
        $apiMailbox = new ApiMailbox;

        $apiMailbox->setName($mailbox->getName())
            ->setDomainId($mailbox->getDomain()->getId())
            ->setId($mailbox->getId());

        return $apiMailbox;
    }
}
