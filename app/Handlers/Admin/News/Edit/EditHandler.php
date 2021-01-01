<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\News\Edit;

use app\DataTransferObjects\Admin\News\EditNews;
use app\Exceptions\News\NewsNotFoundException;
use app\Repository\News\NewsRepository;

class EditHandler
{
    /**
     * @var NewsRepository
     */
    private $repository;

    public function __construct(NewsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(EditNews $dto): void
    {
        $news = $this->repository->find($dto->getId());

        if ($news === null) {
            throw NewsNotFoundException::byId($dto->getId());
        }

        $news
            ->setTitle($dto->getTitle())
            ->setContent($dto->getContent());

        $this->repository->update($news);
    }
}
