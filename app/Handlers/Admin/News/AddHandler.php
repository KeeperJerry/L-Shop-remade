<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\News;

use app\DataTransferObjects\Admin\News\Add;
use app\Entity\News;
use app\Repository\News\NewsRepository;
use app\Services\Auth\Auth;

class AddHandler
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var NewsRepository
     */
    private $repository;

    public function __construct(Auth $auth, NewsRepository $repository)
    {
        $this->auth = $auth;
        $this->repository = $repository;
    }

    public function handle(Add $dto): void
    {
        $this->repository->create(new News(
            $dto->getTitle(),
            $dto->getContent(),
            $this->auth->getUser()
        ));
    }
}
