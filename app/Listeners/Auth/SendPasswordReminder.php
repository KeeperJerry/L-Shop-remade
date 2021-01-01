<?php
declare(strict_types = 1);

namespace app\Listeners\Auth;

use app\Events\Auth\PasswordReminderCreatedEvent;
use app\Mail\Auth\Reminder;
use Illuminate\Contracts\Mail\Mailer;

class SendPasswordReminder
{
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer= $mailer;
    }

    public function handle(PasswordReminderCreatedEvent $event): void
    {
        $this->mailer
            ->to($event->getReminder()->getUser()->getEmail())
            ->queue(new Reminder($event->getReminder(), $event->getIp()));
    }
}
