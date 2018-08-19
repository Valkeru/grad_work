<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 17.08.18
 * Time: 1:08
 */

namespace App\EventSubscriber;

use App\Entity\Customer;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\TraceableValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DoctrineEventSubscriber implements EventSubscriber
{
    /**
     * @var TraceableValidator
     */
    private $validator;

    private $logger;

    public function __construct(ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->validator = $validator;
        $this->logger    = $logger;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist
        ];
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getEntity();
        /** @var ConstraintViolationList $violations */
        $violations = $this->validator->validate($entity);

        if (\count($violations) !== 0) {
            $message = new \stdClass();
            switch (\get_class($entity)) {
                case Customer::class:
                    foreach ($violations as $violation) {
                        $property = $violation->getPropertyPath();
                        if ($property === 'password') {
                            $this->logger->error(json_encode([
                                'customer_login' => $entity->getLogin(),
                                'violation'      => $violation->getMessage()
                            ]));
                            continue;
                        }

                        $message->$property = $violation->getMessage();
                    }

                    break;
                default:
                    foreach ($violations as $violation) {
                        $property           = $violation->getPropertyPath();
                        $message->$property = $violation->getMessage();
                    }
                    break;
            }

            throw new ValidatorException(json_encode($message));
        }
    }
}
