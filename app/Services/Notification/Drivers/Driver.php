<?php
declare(strict_types = 1);

namespace app\Services\Notification\Drivers;

use app\Services\Notification\Notification;

interface Driver
{
    public function push(Notification $notification): void;

    public function pull(): array;

    public function flush(): void;
}
