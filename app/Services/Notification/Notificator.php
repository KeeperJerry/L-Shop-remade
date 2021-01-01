<?php
declare(strict_types = 1);

namespace app\Services\Notification;

use app\Services\Notification\Drivers\Driver;

class Notificator
{
    /**
     * @var Driver
     */
    private $driver;

    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    public function notify(Notification $notification)
    {
        $this->driver->push($notification);
    }

    public function pull(): array
    {
        return $this->driver->pull();
    }
}
