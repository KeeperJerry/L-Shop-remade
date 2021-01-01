<?php
declare(strict_types = 1);

namespace app\Handlers\Console\DB;

use app\Repository\User\UserRepository;
use app\Services\Database\GarbageCollection\GarbageCollector;
use app\Services\User\RolesInitializer;

class AttachDefaultRolesToAllUsers
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RolesInitializer
     */
    private $rolesInitializer;

    /**
     * @var GarbageCollector
     */
    private $gc;

    public function __construct(UserRepository $userRepository, RolesInitializer $rolesInitializer, GarbageCollector $gc)
    {
        $this->userRepository = $userRepository;
        $this->rolesInitializer = $rolesInitializer;
        $this->gc = $gc;
    }

    public function handle(): void
    {
        foreach ($this->userRepository->findAllAsIterable() as $user) {
            // $user[0] is always the user object.
            $this->rolesInitializer->attachDefaultRoles($user[0]);
            // Clean up the internal storage so as not to wake from memory
            $this->gc->collectAll();
        }
    }
}
