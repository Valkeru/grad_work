<?php

namespace App\Command;

use App\Entity\Customer;
use App\Entity\Database;
use App\Entity\Domain;
use App\Entity\Mailbox;
use App\Repository\DomainRepository;
use App\Repository\MailboxRepository;
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

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->container->get('doctrine.orm.default_entity_manager');

        /** @var DomainRepository $dRepo */
        $dRepo = $manager->getRepository(Domain::class);
        /** @var MailboxRepository $mRepo */
        $mRepo = $manager->getRepository(Mailbox::class);

        $domain = $dRepo->find(1);
        $domain2 = $dRepo->find(2);

        $m1 = $mRepo->findByDomain($domain)->all();
        $m2 = $mRepo->findById(5)->one();

        usleep(0);
    }
}
