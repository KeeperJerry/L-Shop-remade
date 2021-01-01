<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Frontend\News;

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
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'title' => $this->news->getTitle(),
            'content' => $this->news->getContent(),
            'user' => $this->news->getUser()->getUsername(),
            'publishedAt' => (new JavaScriptFormatter())->format($this->news->getCreatedAt())
        ];
    }
}
