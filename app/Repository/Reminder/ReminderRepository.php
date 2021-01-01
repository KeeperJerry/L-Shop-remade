<?php
declare(strict_types = 1);

namespace app\Repository\Reminder;

use app\Entity\Reminder;
use app\Entity\User;

interface ReminderRepository
{
    public function create(Reminder $reminder): void;

    public function deleteAll(): bool;

    public function findByCode(string $code): ?Reminder;

    public function remove(Reminder $reminder): void;

    public function deleteByUser(User $user): void;
}
