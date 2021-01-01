<?php
declare(strict_types = 1);

namespace app\Services\Purchasing\Distributors;

use app\Entity\Distribution;
use app\Jobs\Purchasing\Distribution\DistributeByRcon;
use app\Services\Purchasing\Distributors\RconDistribution\CommandBuilder;
use Illuminate\Contracts\Bus\Dispatcher;

/**
 * Class RconDistributor
 * Produces the delivery of products by RCON protocol.
 *
 * @see http://wiki.vg/RCON
 */
class RconDistributor implements Distributor, Attempting
{
    /**
     * @var CommandBuilder
     */
    private $commandBuilder;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(
        CommandBuilder $commandBuilder,
        Dispatcher $dispatcher)
    {
        $this->commandBuilder = $commandBuilder;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function distribute(Distribution $distribution): void
    {
        $commands = $this->commandBuilder->build($distribution->getPurchaseItem());
        $this->dispatcher->dispatch(new DistributeByRcon(
            $distribution,
            $commands
        ));
    }
}
