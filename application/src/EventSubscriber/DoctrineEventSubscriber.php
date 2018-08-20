<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 17.08.18
 * Time: 1:08
 */

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
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

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
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
        $entity   = $eventArgs->getEntity();
        $manager  = $eventArgs->getEntityManager();
        $metadata = $manager->getClassMetadata(\get_class($entity));

        if (\count($metadata->entityListeners) !== 0) {
            return;
        }

        /** @var ConstraintViolationList $violations */
        $violations = $this->validator->validate($entity);

        if (\count($violations) !== 0) {
            $message = new \stdClass();
            foreach ($violations as $violation) {
                $property           = $violation->getPropertyPath();
                $message->$property = $violation->getMessage();
            }

            throw new ValidatorException(json_encode($message));
        }
    }
}
