<?php
declare(strict_types = 1);

namespace app\Listeners\Auth;

use app\Events\Auth\RegistrationSuccessfulEvent;
use app\Services\User\RolesInitializer;

class AttachDefaultRoles
{
    /**
     * @var RolesInitializer
     */
    private $rolesInitializer;

    /**
     * Create the event listener.
     *
     * @param RolesInitializer $rolesInitializer
     */
    public function __construct(RolesInitializer $rolesInitializer)
    {
        $this->rolesInitializer = $rolesInitializer;
    }

    /**
     * Handle the event.
     *
     * @param  RegistrationSuccessfulEvent $event
     */
    public function handle(RegistrationSuccessfulEvent $event): void
    {
        $this->rolesInitializer->attachDefaultRoles($event->getUser());
    }
}
