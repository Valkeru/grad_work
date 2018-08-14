<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 14.08.18
 * Time: 18:31
 */

namespace App\Security;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CustomerProvider implements UserProviderInterface
{
    private $repository;

    /**
     * CustomerTokenProvider constructor.
     *
     * @param CustomerRepository $repository
     */
    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function loadUserByUsername($username)
    {
        try {
            return $this->repository->findByLogin($username);
        } catch (NoResultException | NonUniqueResultException $e) {
            throw new UsernameNotFoundException('User not found');
        }
    }

    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException('User refreshing is unsupported');
    }

    public function supportsClass($class): bool
    {
        return Customer::class === $class;
    }

}
