<?php
declare(strict_types = 1);

namespace app\Services\Notification\Notifications;

use app\Services\Notification\Notification;

class Info implements Notification
{
    private $type = 'info';

    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function content()
    {
        return [
            'type' => $this->type,
            'content' => $this->content
        ];
    }
}
