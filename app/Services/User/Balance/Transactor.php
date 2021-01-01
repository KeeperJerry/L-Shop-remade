<?php
declare(strict_types = 1);

namespace app\Services\User\Balance;

use app\Entity\BalanceTransaction;
use app\Entity\User;
use app\Exceptions\InvalidArgumentException;
use app\Repository\BalanceTransaction\BalanceTransactionRepository;
use app\Repository\User\UserRepository;

/**
 * Class Transactor
 * The class performs add/sub/set of the user's balance with the logging of transaction information.
 */
class Transactor
{
    /**
     * @var BalanceTransactionRepository
     */
    private $transactionRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(BalanceTransactionRepository $repository, UserRepository $userRepository)
    {
        $this->transactionRepository = $repository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param User  $user
     * @param float $value
     *
     * @return float New balance.
     */
    public function add(User $user, float $value): float
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('The argument $value must be a positive number');
        }

        $this->transactionRepository->create(new BalanceTransaction($value, $user));
        $user->setBalance($user->getBalance() + $value);
        $this->userRepository->update($user);

        return $user->getBalance();
    }

    /**
     * @param User  $user
     * @param float $value
     *
     * @return float New balance.
     */
    public function sub(User $user, float $value): float
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('The argument $value must be a positive number');
        }

        // When debiting money from the balance, the delta must be negative number.
        $this->transactionRepository->create(new BalanceTransaction($value * -1, $user));
        $user->setBalance($user->getBalance() - $value);
        $this->userRepository->update($user);

        return $user->getBalance();
    }

    /**
     * @param User  $user
     * @param float $value
     */
    public function set(User $user, float $value): void
    {
        if ($value < 0) {
            throw new InvalidArgumentException('The argument $value must be a equals or greater than 0');
        }

        $this->transactionRepository->create(new BalanceTransaction($value - $user->getBalance(), $user));
        $user->setBalance($value);
        $this->userRepository->update($user);
    }
}
