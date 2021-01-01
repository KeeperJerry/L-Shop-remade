<?php
declare(strict_types = 1);

namespace app\Services\Server;

use app\Entity\Server;
use app\Entity\User;
use app\Services\Auth\Permissions;

class ServerAccess
{
    /**
     * Private constructor because this class contains only static methods.
     */
    private function __construct()
    {
    }

    public static function isUserHasAccessTo(?User $user, Server $server): bool
    {
        return ($server->isEnabled() || ($user !== null && $user->hasPermission(Permissions::ACCESS_TO_DISABLED_SERVER)));
    }
}
