<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Auth;

use app\Services\Auth\Activator;

class CompleteActivationHandler
{
    /**
     * @var Activator
     */
    private $activator;

    public function __construct(Activator $activator)
    {
        $this->activator = $activator;
    }

    public function handle(string $code): bool
    {
        return $this->activator->complete($code);
    }
}
