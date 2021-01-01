<?php
declare(strict_types=1);

namespace app\Services\Auth;

use app\Entity\User;
use app\Events\Auth\RegistrationBeginEvent;
use app\Events\Auth\RegistrationFailedEvent;
use app\Events\Auth\RegistrationSuccessfulEvent;
use app\Services\Auth\Checkpoint\Pool;
use app\Services\Auth\Session\Session;
use app\Services\Auth\Session\SessionPersistence;
use Illuminate\Events\Dispatcher;
use Psr\Log\LoggerInterface;

class DefaultAuth implements Auth
{
    /**
     * @var Authenticator
     */
    private $authenticator;

    /**
     * @var Registrar
     */
    private $registrar;

    /**
     * @var SessionPersistence
     */
    private $sessionPersistence;

    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Session
     */
    private $session = null;

    public function __construct(
        Authenticator $authenticator,
        Registrar $registrar,
        SessionPersistence $sessionPersistence,
        Dispatcher $dispatcher,
        Pool $pool,
        LoggerInterface $logger)
    {
        $this->authenticator = $authenticator;
        $this->registrar = $registrar;
        $this->sessionPersistence = $sessionPersistence;
        $this->eventDispatcher = $dispatcher;
        $this->pool = $pool;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(string $username, string $password, bool $remember = false): bool
    {
        $this->session = $this->authenticator->authenticate($username, $password, $remember);

        return $this->session->check();
    }

    /**
     * {@inheritdoc}
     */
    public function authenticateQuick(User $user, bool $remember): bool
    {
        $this->session = $this->authenticator->authenticateQuick($user, $remember);

        return $this->session->check();
    }

    /**
     * {@inheritdoc}
     */
    public function register(User $user, bool $activate = false): User
    {
        $this->eventDispatcher->dispatch(new RegistrationBeginEvent());
        try {
            $user = $this->registrar->register($user);
        } catch (\Exception $e) {
            $this->logger->error($e);
            $this->eventDispatcher->dispatch(new RegistrationFailedEvent());

            throw $e;
        }
        $this->eventDispatcher->dispatch(new RegistrationSuccessfulEvent($user, $activate));

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(): ?User
    {
        $this->setSessionIfNeed();

        return $this->session->getUser();
    }

    /**
     * {@inheritdoc}
     */
    public function check(): bool
    {
        $this->setSessionIfNeed();

        return $this->session->check();
    }

    /**
     * {@inheritdoc}
     */
    public function logout(bool $anywhere = false): void
    {
        $this->pool->disable();
        $this->setSessionIfNeed();
        if ($this->session->check()) {
            $this->sessionPersistence->destroy($this->session->getUser(), $anywhere);
            $this->session = $this->sessionPersistence->createEmpty();
        }
        $this->pool->reset();
    }

    private function setSessionIfNeed(): void
    {
        if ($this->session === null) {
            $this->session = $this->sessionPersistence->createFromPersistenceStorage();
        }
    }
}
