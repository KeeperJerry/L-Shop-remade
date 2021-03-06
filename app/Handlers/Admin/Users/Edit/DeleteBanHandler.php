<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users\Edit;

use app\Exceptions\Ban\BanNotFoundException;
use app\Repository\Ban\BanRepository;

class DeleteBanHandler
{
    /**
     * @var BanRepository
     */
    private $banRepository;

    public function __construct(BanRepository $banRepository)
    {
        $this->banRepository = $banRepository;
    }

    /**
     * @param int $banId
     *
     * @throws BanNotFoundException
     */
    public function handle(int $banId): void
    {
        $ban = $this->banRepository->find($banId);
        if ($ban === null) {
            throw BanNotFoundException::byId($banId);
        }

        $this->banRepository->remove($ban);
    }
}
