<?php
declare(strict_types = 1);

namespace app\Services\Game\Permissions;

use app\Entity\User;

class PlayerWithUser extends Player
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user, Group $primaryGroup)
    {
        parent::__construct($primaryGroup);
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
