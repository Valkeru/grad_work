<?php

namespace App\Command;

use App\Entity\AccountStatus;
use App\Entity\Customer;
use App\Entity\Server;
use App\Repository\ServerRepository;
use Predis\Client;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Cache\Simple\RedisCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\TraceableValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        $em = $this->container->get('doctrine.orm.entity_manager');
        /** @var Server $srv */
        $srv = $em->getRepository(Server::class)->find(1);

        $c = $srv->getCustomers()->toArray();

        return;
    }
}
