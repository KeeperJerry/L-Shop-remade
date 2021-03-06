<?php
declare(strict_types = 1);

namespace app\Services\Auth\Acl;

use Doctrine\Common\Collections\Collection;

/**
 * Trait RoleTrait
 * Represents functionality for checking for roles.
 */
trait RoleTrait
{
    /**
     * {@inheritdoc}
     */
    public function hasRole($role): bool
    {
        /** @var RoleInterface $each */
        foreach ($this->getRoles() as $each) {
            if ($each instanceof RoleInterface) {
                if ($role->getName() === $each->getName()) {
                    return true;
                }
            } else {
                if ($role === $each->getName()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAllRoles(array $roles): bool
    {
        if (count($this->getRoles()) === 0) {
            return false;
        }

        /** @var PermissionInterface $each */
        foreach ($this->getRoles() as $each) {
            /** @var RoleInterface $role */
            foreach ($roles as $role) {
                if ($role->getName() !== $each->getName()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAtLeastOneRole(array $roles): bool
    {
        /** @var PermissionInterface $each */
        foreach ($this->getRoles() as $each) {
            /** @var RoleInterface $role */
            foreach ($roles as $role) {
                if ($role->getName() === $each->getName()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return Collection
     */
    abstract public function getRoles(): Collection;
}
