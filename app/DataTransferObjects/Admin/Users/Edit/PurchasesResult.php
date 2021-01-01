<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Admin\Users\Edit;

use app\DataTransferObjects\Frontend\Profile\Purchases\Purchase;
use app\Services\Response\JsonRespondent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PurchasesResult implements JsonRespondent
{
    /**
     * @var LengthAwarePaginator
     */
    private $paginator;

    /**
     * @var Purchase[]
     */
    private $purchases = [];

    public function __construct(LengthAwarePaginator $paginator)
    {
        $this->paginator = $paginator;
        foreach ($this->paginator->items() as $item) {
            $this->purchases[] = new Purchase($item);
        }
    }

    /**
     * @inheritDoc
     */
    public function response(): array
    {
        return [
            'paginator' => $this->paginator,
            'purchases' => $this->purchases
        ];
    }
}
