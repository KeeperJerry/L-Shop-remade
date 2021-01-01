<?php
declare(strict_types = 1);

namespace app\Services\Auth;

use app\Entity\User;
use app\Repository\User\UserRepository;
use app\Services\Auth\Checkpoint\Pool;
use app\Services\Auth\Hashing\Hasher;
use app\Services\Auth\Session\Session;
use app\Services\Auth\Session\SessionPersistence;

class DefaultAuthenticator implements Authenticator
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * @var SessionPersistence
     */
    private $sessionPersistence;

    /**
     * @var Pool
     */
    private $checkpointPool;

    public function __construct(
        UserRepository $userRepository,
        Hasher $hasher,
        SessionPersistence $sessionPersistence,
        Pool $checkpointsPool)
    {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
        $this->sessionPersistence = $sessionPersistence;
        $this->checkpointPool = $checkpointsPool;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(string $username, string $password, bool $remember): Session
    {
        $user = $this->userRepository->findByUsername($username);
        if ($user === null) {
            $this->checkpointPool->passLoginFail();

            return $this->emptySession();
        }

        // If passwords are equals.
        if (!$this->hasher->check($password, $user->getPassword())) {
            $this->checkpointPool->passLoginFail($user);

            return $this->emptySession();
        }

        if (!$this->checkpointPool->passLogin($user)) {
            return $this->emptySession();
        }

        return $this->sessionPersistence->createFromUser($user, $remember);
    }

    /**
     * {@inheritdoc}
     */
    public function authenticateQuick(User $user, bool $remember): Session
    {
        if (!$this->checkpointPool->passLogin($user)) {
            return $this->emptySession();
        }

        return $this->sessionPersistence->createFromUser($user, $remember);
    }

    private function emptySession(): Session
    {
        return $this->sessionPersistence->createEmpty();
    }
}
