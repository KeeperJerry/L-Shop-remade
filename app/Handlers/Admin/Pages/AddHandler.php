<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Pages;

use app\DataTransferObjects\Admin\Pages\Add;
use app\Entity\Page;
use app\Exceptions\Page\AlreadyExistException;
use app\Repository\Page\PageRepository;

class AddHandler
{
    /**
     * @var PageRepository
     */
    private $repository;

    public function __construct(PageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(Add $add): void
    {
        $page = $this->repository->findByUrl($add->getUrl());
        if ($page !== null) {
            throw new AlreadyExistException($add->getUrl());
        }

        $this->repository->create(new Page($add->getTitle(), $add->getContent(), $add->getUrl()));
    }
}
