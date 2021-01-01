<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Pages;

use app\Exceptions\Page\PageNotFoundException;
use app\Repository\Page\PageRepository;

class DeleteHandler
{
    /**
     * @var PageRepository
     */
    private $repository;

    public function __construct(PageRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $pageId
     *
     * @throws PageNotFoundException
     */
    public function handle(int $pageId): void
    {
        $page = $this->repository->find($pageId);
        if ($page === null) {
            throw PageNotFoundException::byId($pageId);
        }

        $this->repository->remove($page);
    }
}
