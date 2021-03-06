<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users\Edit;

use app\DataTransferObjects\Admin\Users\Edit\Edit;
use app\Entity\User;
use app\Exceptions\User\UserNotFoundException;
use app\Repository\Permission\PermissionRepository;
use app\Repository\Role\RoleRepository;
use app\Repository\User\UserRepository;
use app\Services\Auth\Exceptions\EmailAlreadyExistsException;
use app\Services\Auth\Exceptions\UsernameAlreadyExistsException;
use app\Services\Auth\Hashing\Hasher;
use app\Services\User\Balance\Transactor;

class EditHandler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * @var PermissionRepository
     */
    private $permissionRepository;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * @var Transactor
     */
    private $transactor;

    public function __construct(
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        PermissionRepository $permissionRepository,
        Hasher $hasher,
        Transactor $transactor)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->hasher = $hasher;
        $this->transactor = $transactor;
    }

    /**
     * @param Edit $dto
     *
     * @throws UserNotFoundException
     */
    public function handle(Edit $dto)
    {
        $user = $this->userRepository->find($dto->getUserId());
        if ($user === null) {
            throw UserNotFoundException::byId($dto->getUserId());
        }

        $userByUsername = $this->userRepository->findByUsername($dto->getUsername());
        if ($userByUsername !== null && $userByUsername->getId() !== $user->getId()) {
            throw new UsernameAlreadyExistsException($dto->getUsername());
        }

        $userByEmail = $this->userRepository->findByEmail($dto->getEmail());
        if ($userByEmail !== null && $userByEmail->getId() !== $user->getId()) {
            throw new EmailAlreadyExistsException($dto->getEmail());
        }

        $user
            ->setUsername($dto->getUsername())
            ->setEmail($dto->getEmail());

        if (!empty($dto->getPassword())) {
            $user->setPassword($this->hasher->make($dto->getPassword()));
        }

        $this->updateRoles($user, $dto->getRoles());
        $this->updatePermissions($user, $dto->getPermissions());

        $this->userRepository->update($user);

        $this->transactor->set($user, $dto->getBalance());
    }

    private function updateRoles(User $user, array $roles): void
    {
        $roles = $this->roleRepository->findWhereNameIn($roles);
        $old = $user->getRoles();
        // Attach roles.
        foreach ($roles as $role) {
            if ($old->indexOf($role) === false) {
                $role->getUsers()->add($user);
                $user->getRoles()->add($role);
            }
        }

        // Detach roles.
        foreach ($old as $item) {
            $f = false;
            foreach ($roles as $role) {
                if ($role->getId() === $item->getId()) {
                    $f = true;
                }
            }

            if (!$f) {
                $item->getUsers()->removeElement($user);
                $user->getRoles()->removeElement($item);
            }
        }
    }

    private function updatePermissions(User $user, array $permissions): void
    {
        $permissions = $this->permissionRepository->findWhereNameIn($permissions);
        $old = $user->getPermissions();
        // Attach permissions.
        foreach ($permissions as $permission) {
            if ($old->indexOf($permission) === false) {
                $permission->getUsers()->add($user);
                $user->getPermissions()->add($permission);
            }
        }

        // Detach permissions.
        foreach ($old as $item) {
            $f = false;
            foreach ($permissions as $permission) {
                if ($permission->getId() === $item->getId()) {
                    $f = true;
                }
            }

            if (!$f) {
                $item->getUsers()->removeElement($user);
                $user->getPermissions()->removeElement($item);
            }
        }
    }
}
