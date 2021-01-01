<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Pages\Edit;

use app\DataTransferObjects\Admin\Pages\Edit\Edit;
use app\Exceptions\Page\PageNotFoundException;
use app\Repository\Page\PageRepository;

class EditHandler
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
     * @param Edit $dto
     *
     * @throws PageNotFoundException
     */
    public function handle(Edit $dto): void
    {
        $page = $this->repository->find($dto->getId());
        if ($page === null) {
            throw PageNotFoundException::byId($dto->getId());
        }

        $page
            ->setTitle($dto->getTitle())
            ->setContent($dto->getContent())
            ->setUrl($dto->getUrl());

        $this->repository->update($page);
    }
}
