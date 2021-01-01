<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Profile\Character;

use app\Repository\User\UserRepository;
use app\Services\Auth\Auth;
use app\Services\Auth\Hashing\Hasher;

class ChangePasswordHandler
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var Hasher
     */
    private $hasher;

    public function __construct(Auth $auth, UserRepository $repository, Hasher $hasher)
    {
        $this->auth = $auth;
        $this->repository = $repository;
        $this->hasher = $hasher;
    }

    public function handle(string $newPassword): void
    {
        $this->repository->update($this->auth->getUser()->setPassword($this->hasher->make($newPassword)));
    }
}
