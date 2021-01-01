<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Auth;

use app\Mail\Auth\Confirmation;
use app\Repository\User\UserRepository;
use app\Services\Auth\Activator;
use app\Services\Auth\Exceptions\AlreadyActivatedException;
use app\Services\Auth\Exceptions\UserDoesNotExistException;
use Illuminate\Contracts\Mail\Mailer;

class RepeatActivationHandler
{
    /**
     * @var Activator
     */
    private $activator;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Activator $activator, UserRepository $userRepository, Mailer $mailer)
    {
        $this->activator = $activator;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }

    public function handle(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);
        if ($user === null) {
            throw new UserDoesNotExistException($email);
        }
        if ($this->activator->isActivated($user)) {
            throw new AlreadyActivatedException($user);
        }

        $activation = $this->activator->makeActivation($user);
        $this->mailer
            ->to($activation->getUser()->getEmail())
            ->queue(new Confirmation($activation));
    }
}
