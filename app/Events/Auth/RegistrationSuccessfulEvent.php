<?php
declare(strict_types = 1);

namespace app\Events\Auth;

use app\Entity\User;

class RegistrationSuccessfulEvent
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var bool
     */
    private $needActivate;

    public function __construct(User $user, bool $needActivate)
    {
        $this->user = $user;
        $this->needActivate = $needActivate;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isNeedActivate(): bool
    {
        return $this->needActivate;
    }
}
