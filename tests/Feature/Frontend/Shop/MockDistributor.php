<?php
declare(strict_types = 1);

namespace Tests\Feature\Frontend\Shop;

use app\Entity\Distribution;
use app\Services\Purchasing\Distributors\Distributor;

class MockDistributor implements Distributor
{
    /**
     * @inheritDoc
     */
    public function distribute(Distribution $distribution): void
    {
        //
    }
}
