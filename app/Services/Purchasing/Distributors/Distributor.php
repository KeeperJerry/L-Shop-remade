<?php
declare(strict_types = 1);

namespace app\Services\Purchasing\Distributors;

use app\Entity\Distribution;
use app\Exceptions\Distributor\DistributionException;

/**
 * Interface Distributor
 * Produces the delivery of products to the player.
 */
interface Distributor
{
    /**
     * Produces the delivery of products to the player.
     *
     * @param Distribution $distribution
     *
     * @throws DistributionException
     */
    public function distribute(Distribution $distribution): void;
}
