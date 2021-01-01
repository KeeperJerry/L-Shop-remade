<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\News;

use app\DataTransferObjects\Frontend\News\News as DTO;
use app\Exceptions\News\NewsNotFoundException;
use app\Repository\News\NewsRepository;

class VisitHandler
{
    /**
     * @var NewsRepository
     */
    private $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    /**
     * @param int $newsId
     *
     * @return DTO
     * @throws NewsNotFoundException
     */
    public function handle(int $newsId): DTO
    {
        $news = $this->newsRepository->find($newsId);
        if ($news === null) {
            throw NewsNotFoundException::byId($newsId);
        }

        return new DTO($news);
    }
}
