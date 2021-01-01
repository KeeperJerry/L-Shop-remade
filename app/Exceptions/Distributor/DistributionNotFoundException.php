<?php
declare(strict_types = 1);

namespace app\Exceptions\Distributor;

use app\Exceptions\DomainException;

class DistributionNotFoundException extends DomainException
{
    public static function byId(int $id): DistributionNotFoundException
    {
        return new DistributionNotFoundException("Distribution with id {$id} not found");
    }
}
