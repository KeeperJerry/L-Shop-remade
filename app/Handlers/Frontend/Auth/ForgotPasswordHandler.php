<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Auth;

use app\Repository\User\UserRepository;
use app\Services\Auth\Exceptions\UserDoesNotExistException;
use app\Services\Auth\Reminder;

class ForgotPasswordHandler
{
    /**
     * @var Reminder
     */
    private $reminder;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(Reminder $reminder, UserRepository $userRepository)
    {
        $this->reminder = $reminder;
        $this->userRepository = $userRepository;
    }

    public function handle(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);
        if ($user === null) {
            throw new UserDoesNotExistException($email);
        }
        $this->reminder->makeReminder($user);
    }
}
