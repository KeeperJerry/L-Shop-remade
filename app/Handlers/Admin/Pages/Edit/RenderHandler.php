<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Pages\Edit;

use app\DataTransferObjects\Admin\Pages\Edit\Page;
use app\Exceptions\Page\PageNotFoundException;
use app\Repository\Page\PageRepository;

class RenderHandler
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
     * @return Page
     *
     * @throws PageNotFoundException
     */
    public function handle(int $pageId): Page
    {
        $page = $this->repository->find($pageId);
        if ($page === null) {
            throw PageNotFoundException::byId($pageId);
        }

        return new Page($page);
    }
}
