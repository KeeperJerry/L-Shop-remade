<?php
declare(strict_types = 1);

namespace app\Handlers\Consoe\User\Roles;

use app\DataTransferObjects\Commands\User\Roles\RolesList;
use app\Exceptions\User\UserNotFoundException;
use app\Repository\User\UserRepository;

class ListHandler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $username The user whose list of roles need to get.
     *
     * @return RolesList
     * @throws UserNotFoundException
     */
    public function handle(string $username): RolesList
    {
        $user = $this->userRepository->findByUsername($username);
        if ($user === null) {
            throw UserNotFoundException::byUsername($username);
        }

        return new RolesList($user->getRoles()->toArray(), $user);
    }
}
