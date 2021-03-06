<?php

namespace App\Security;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class EmployeeProvider implements UserProviderInterface
{
    /**
     * @var EmployeeRepository
     */
    private $repository;

    public function __construct(EmployeeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $username
     *
     * @return Employee
     * @throws UsernameNotFoundException
     */
    public function loadUserByUsername($username): Employee
    {
        try {
            return $this->repository->findByLogin($username)->strict()->one();
        } catch (NotFoundHttpException $e) {
            throw new UsernameNotFoundException('User not found');
        }
    }

    /**
     * @param UserInterface $user
     *
     * @return UserInterface|void
     */
    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException('User refreshing is unsupported');
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return Employee::class === $class;
    }

}
