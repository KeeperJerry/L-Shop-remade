<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Admin\Users;

use app\Services\Auth\Activator;
use app\Services\Auth\BanManager;
use app\Services\Response\JsonRespondent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListResult implements JsonRespondent
{
    /**
     * @var LengthAwarePaginator
     */
    private $paginator;

    /**
     * @var User[]
     */
    private $users = [];

    public function __construct(LengthAwarePaginator $paginator, Activator $activator, BanManager $banManager)
    {
        $this->paginator = $paginator;
        foreach ($this->paginator->items() as $item) {
            $this->users[] = new User($item, $activator->isActivated($item), $banManager->isBanned($item));
        }
    }

    /**
     * @inheritDoc
     */
    public function response(): array
    {
        return [
            'paginator' => $this->paginator,
            'users' => $this->users
        ];
    }
}
