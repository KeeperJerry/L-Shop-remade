<?php
declare(strict_types = 1);

namespace app\Services\Notification;

interface Notification
{
    /**
     * The data returned from this method will be used by the notification driver
     * for storage and distribution.
     *
     * @return mixed
     */
    public function content();
}
