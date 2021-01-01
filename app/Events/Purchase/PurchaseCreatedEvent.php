<?php
declare(strict_types = 1);

namespace app\Events\Purchase;

use app\Entity\Purchase;

class PurchaseCreatedEvent
{
    /**
     * @var Purchase
     */
    private $purchase;

    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    public function getPurchase(): Purchase
    {
        return $this->purchase;
    }
}
