<?php
declare(strict_types = 1);

namespace app\Listeners\Auth;

use app\Events\Auth\RegistrationSuccessfulEvent;
use app\Mail\Auth\Confirmation;
use app\Services\Auth\Activator;
use app\Services\Auth\Checkpoint\Pool;
use Illuminate\Contracts\Mail\Mailer;

class SendEmailConfirmation
{
    /**
     * @var Activator
     */
    private $activator;

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Activator $activator, Pool $pool, Mailer $mailer)
    {
        $this->activator = $activator;
        $this->pool = $pool;
        $this->mailer = $mailer;
    }

    public function handle(RegistrationSuccessfulEvent $event): void
    {
        if ($event->isNeedActivate()) {
            $this->activator->activate($event->getUser());
        } else {
            $activation = $this->activator->makeActivation($event->getUser());
            $this->mailer
                ->to($event->getUser()->getEmail())
                ->queue(new Confirmation($activation));
        }
    }
}
