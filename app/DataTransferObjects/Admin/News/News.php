<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Admin\News;

use app\Entity\News as Entity;
use app\Services\DateTime\Formatting\JavaScriptFormatter;

class News implements \JsonSerializable
{
    /**
     * @var Entity
     */
    private $news;

    public function __construct(Entity $news)
    {
        $this->news = $news;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->news->getId(),
            'title' => $this->news->getTitle(),
            'user' => [
                'username' => $this->news->getUser()->getUsername()
            ],
            'created_at' => (new JavaScriptFormatter())->format($this->news->getCreatedAt())
        ];
    }
}
