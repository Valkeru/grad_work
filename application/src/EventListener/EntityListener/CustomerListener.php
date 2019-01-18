<?php

namespace App\EventListener\EntityListener;

use App\Entity\Customer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class CustomerListener
 *
 * @package App\EventListener\EntityListener
 */
class CustomerListener
{
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CustomerListener constructor.
     *
     * @param ValidatorInterface $validator
     * @param LoggerInterface    $logger
     */
    public function __construct(ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->validator = $validator;
        $this->logger    = $logger;
    }

    /**
     * @param Customer           $customer
     * @param LifecycleEventArgs $args
     */
    public function prePersist(Customer $customer, LifecycleEventArgs $args): void
    {
        /** @var ConstraintViolationList $violations */
        $violations = $this->validator->validate($customer);

        if (\count($violations) !== 0) {
            $message = new \stdClass();
            foreach ($violations as $violation) {
                $property = $violation->getPropertyPath();
                if ($property === 'password') {
                    $this->logger->error(json_encode([
                        'customer_login' => $customer->getLogin(),
                        'violation'      => $violation->getMessage()
                    ]));
                    continue;
                }

                $message->$property = $violation->getMessage();
            }

            throw new ValidatorException(json_encode($message));
        }
    }
}
