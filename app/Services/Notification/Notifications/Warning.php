<?php
declare(strict_types = 1);

namespace app\Services\Notification\Notifications;

use app\Services\Notification\Notification;

class Warning implements Notification
{
    private $type = 'warning';

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
