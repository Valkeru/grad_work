<?php

namespace App\Command;

use App\Entity\Customer;
use App\Entity\Database;
use App\Entity\Domain;
use App\Entity\Mailbox;
use App\Repository\DomainRepository;
use App\Repository\MailboxRepository;
use App\Service\RegistrationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'test';

    /**
     * @var Container
     */
    private $container;

    private $cache;

    public function __construct(ContainerInterface $container, RegistrationService $registrationService)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->container->get('doctrine.orm.default_entity_manager');
    }
}
