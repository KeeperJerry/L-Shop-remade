<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Auth;

use app\DataTransferObjects\Frontend\Auth\LoginResult;
use app\Exceptions\ForbiddenException;
use app\Services\Auth\AccessMode;
use app\Services\Auth\Auth;
use app\Services\Auth\Permissions;
use app\Services\Settings\Settings;

class LoginHandler
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Settings
     */
    private $settings;

    public function __construct(Auth $auth, Settings $settings)
    {
        $this->auth = $auth;
        $this->settings = $settings;
    }

    public function handle(string $username, string $password, bool $remember): LoginResult
    {
        $result = $this->auth->authenticate($username, $password, $remember);
        if ($result &&
            $this->settings->get('auth.access_mode')->getValue() === AccessMode::GUEST &&
            !$this->auth->getUser()->hasPermission(Permissions::ACCESS_WHILE_ACCESS_MODE_GUEST)
        ) {
            $this->auth->logout();
            throw new ForbiddenException();
        }

        return new LoginResult($result, $this->auth->getUser());
    }
}
