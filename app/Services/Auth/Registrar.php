<?php
declare(strict_types = 1);

namespace app\Services\Auth;

use app\Entity\User;

interface Registrar
{
    /**
     * Registers a new user.
     *
     * @param User $user The entity of the new user.
     *
     * @return User
     */
    public function register(User $user): User;
}
