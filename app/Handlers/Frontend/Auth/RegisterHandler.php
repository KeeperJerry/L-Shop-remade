<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Auth;

use app\DataTransferObjects\Frontend\Auth\RegisterResult;
use app\Entity\User;
use app\Services\Auth\Auth;
use app\Services\Settings\DataType;
use app\Services\Settings\Settings;

class RegisterHandler
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

    public function handle(string $username, string $email, string $password): RegisterResult
    {
        $sendActivation = $this->settings->get('auth.register.send_activation')->getValue(DataType::BOOL);
        $user = $this->auth->register(new User($username, $email, $password), !$sendActivation);
        $dto = new RegisterResult($user, !$sendActivation);

        return $dto;
    }
}
