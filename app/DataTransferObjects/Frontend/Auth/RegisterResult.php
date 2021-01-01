<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Frontend\Auth;

use app\Entity\User;

class RegisterResult
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var bool
     */
    private $activated;

    public function __construct(?User $user, bool $activated)
    {
        $this->user = $user;
        $this->activated = $activated;
    }

    public function isSuccessfully(): bool
    {
        return $this->user !== null;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
