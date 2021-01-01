<?php
declare(strict_types = 1);

namespace app\Services\Notification;

interface Notificable
{
    /**
     * @return Notification[]
     */
    public function notifications(): array;
}
