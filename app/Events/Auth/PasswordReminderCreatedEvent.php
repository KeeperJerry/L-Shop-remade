<?php
declare(strict_types = 1);

namespace app\Events\Auth;

use app\Entity\Reminder;

class PasswordReminderCreatedEvent
{
    /**
     * @var Reminder
     */
    private $reminder;

    /**
     * @var string
     */
    private $ip;

    public function __construct(Reminder $reminder, string $ip)
    {
        $this->reminder = $reminder;
        $this->ip = $ip;
    }

    public function getReminder(): Reminder
    {
        return $this->reminder;
    }

    public function getIp(): string
    {
        return $this->ip;
    }
}
