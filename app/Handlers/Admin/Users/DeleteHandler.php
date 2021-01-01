<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users;

use app\Exceptions\LogicException;
use app\Exceptions\User\UserNotFoundException;
use app\Repository\User\UserRepository;
use app\Services\Auth\Auth;

class DeleteHandler
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(Auth $auth, UserRepository $userRepository)
    {
        $this->auth = $auth;
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $userId
     *
     * @throws UserNotFoundException
     */
    public function handle(int $userId): void
    {
        $user = $this->userRepository->find($userId);
        if ($user === null) {
            throw UserNotFoundException::byId($userId);
        }

        if ($this->auth->getUser()->getId() === $user->getId()) {
            throw new LogicException('Can not delete yourself');
        }

        $this->userRepository->remove($user);
    }
}
