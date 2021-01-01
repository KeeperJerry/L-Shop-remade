<?php
declare(strict_types = 1);

namespace app\Providers;

use app\Events\Auth\PasswordReminderCreatedEvent;
use app\Events\Auth\RegistrationSuccessfulEvent;
use app\Events\Purchase\PurchaseCompletedEvent;
use app\Events\Purchase\PurchaseCreatedEvent;
use app\Listeners\Auth\AttachDefaultRoles;
use app\Listeners\Auth\SendEmailConfirmation;
use app\Listeners\Auth\SendPasswordReminder;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        RegistrationSuccessfulEvent::class => [
            SendEmailConfirmation::class,
            AttachDefaultRoles::class,
        ],
        PasswordReminderCreatedEvent::class => [
            SendPasswordReminder::class
        ],
        PurchaseCreatedEvent::class => [
            // Register here the listener(s), who must respond to the event creating a purchase.
        ],
        PurchaseCompletedEvent::class => [
            // Register here the listener(s), who must respond to the event completing a purchase.
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
