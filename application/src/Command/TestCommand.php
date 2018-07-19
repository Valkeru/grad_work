<?php

namespace App\Command;

use App\Entity\Customer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\TraceableValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'test';

    /**
     * @var TraceableValidator
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        parent::__construct();
        $this->validator = $validator;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello');
    }
}
