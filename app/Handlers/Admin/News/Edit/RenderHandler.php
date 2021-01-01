<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\News\Edit;

use app\DataTransferObjects\Admin\News\EditNewsRenderResult;
use app\Exceptions\News\NewsNotFoundException;
use app\Repository\News\NewsRepository;

class RenderHandler
{
    /**
     * @var NewsRepository
     */
    private $repository;

    public function __construct(NewsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(int $newsId): EditNewsRenderResult
    {
        $news = $this->repository->find($newsId);

        if ($news === null) {
            throw NewsNotFoundException::byId($newsId);
        }

        return new EditNewsRenderResult($news);
    }
}
