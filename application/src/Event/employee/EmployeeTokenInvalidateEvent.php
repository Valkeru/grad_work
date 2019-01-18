<?php

namespace App\Event\employee;

use App\Entity\Employee;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class EmployeeTokenInvalidateEvent
 *
 * @package App\Event\customer
 */
class EmployeeTokenInvalidateEvent extends Event
{
    public const NAME = 'employee.token_invalidate';

    /**
     * @var Employee
     */
    private $employee;

    /**
     * @var string
     */
    private $tokenString;

    /**
     * EmployeeTokenInvalidateEvent constructor.
     *
     * @param Employee $employee
     * @param string   $tokenString
     */
    public function __construct(Employee $employee, string $tokenString)
    {
        $this->employee    = $employee;
        $this->tokenString = $tokenString;
    }

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    /**
     * @return string
     */
    public function getTokenString(): string
    {
        return $this->tokenString;
    }
}
