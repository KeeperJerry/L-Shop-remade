<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Auth;

use app\Repository\Reminder\ReminderRepository;
use app\Services\Auth\Reminder;

class ResetPasswordHandler
{
    /**
     * @var Reminder
     */
    private $reminder;

    private $repository;

    public function __construct(Reminder $reminder, ReminderRepository $repository)
    {
        $this->reminder = $reminder;
        $this->repository = $repository;
    }

    public function handle(string $code, string $newPassword): bool
    {
        return $this->reminder->complete($code, $newPassword);
    }

    public function isValidCode(string $code): bool
    {
        $entity = $this->repository->findByCode($code);

        return $entity !== null && !$this->reminder->isExpired($entity);
    }
}
