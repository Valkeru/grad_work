<?php

namespace App\Command;

use App\Entity\Customer;
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

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Client $blackListRedis */
        $blackListRedis      = $this->container->get('snc_redis.token_blacklist');
        $blackListRedisCache = new RedisCache($blackListRedis);

        $blackListRedisCache->set('1', [
            (string)Uuid::uuid4() => (new \DateTime())->add(new \DateInterval('P1W'))
        ], 24 * 60 * 60 * 7);

        $data = $blackListRedisCache->get('1');

        return;
    }
}
