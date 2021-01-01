<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\News;

use app\Exceptions\News\NewsNotFoundException;
use app\Repository\News\NewsRepository;

class DeleteHandler
{
    /**
     * @var NewsRepository
     */
    private $repository;

    public function __construct(NewsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(int $newsId): void
    {
        $news = $this->repository->find($newsId);

        if ($news === null) {
            throw NewsNotFoundException::byId($newsId);
        }

        $this->repository->remove($news);
    }
}
