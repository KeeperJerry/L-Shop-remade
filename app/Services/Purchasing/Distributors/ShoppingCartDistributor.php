<?php
declare(strict_types = 1);

namespace app\Services\Purchasing\Distributors;

use app\Entity\Distribution;
use app\Entity\ShoppingCart;
use app\Repository\Distribution\DistributionRepository;
use app\Repository\ShoppingCart\ShoppingCartRepository;
use app\Services\Purchasing\Distributors\ShoppingCartPipeline\OtherPipe;
use app\Services\Purchasing\Distributors\ShoppingCartPipeline\PlayerPipe;
use app\Services\Purchasing\Distributors\ShoppingCartPipeline\SignatureAndAmountPipe;
use app\Services\Purchasing\Distributors\ShoppingCartPipeline\TypePipe;
use Illuminate\Pipeline\Pipeline;

/**
 * Class ShoppingCartDistributor
 * Produces the delivery of products to the player through the plug-in shopping cards reloaded.
 * Implements the pipeline pattern.
 *
 * @see https://github.com/limito/ShoppingCartReloaded
 */
class ShoppingCartDistributor implements Distributor
{
    /**
     * @var ShoppingCartRepository
     */
    private $shoppingCartRepository;

    /**
     * @var DistributionRepository
     */
    private $distributionRepository;

    /**
     * @var Pipeline
     */
    private $pipeline;

    /**
     * @var array
     */
    private $pipes = [
        PlayerPipe::class,
        TypePipe::class,
        SignatureAndAmountPipe::class,
        OtherPipe::class
    ];

    public function __construct(
        ShoppingCartRepository $shoppingCartRepository,
        DistributionRepository $distributionRepository,
        Pipeline $pipeline)
    {
        $this->shoppingCartRepository = $shoppingCartRepository;
        $this->distributionRepository = $distributionRepository;
        $this->pipeline = $pipeline;
    }

    /**
     * {@inheritdoc}
     */
    public function distribute(Distribution $distribution): void
    {
        $this->pipeline
            ->send(new ShoppingCart($distribution))
            ->through($this->pipes)
            ->then(function (ShoppingCart $entity) {
                $this->shoppingCartRepository->create($entity);
                $this->distributionRepository->update($entity->getDistribution());
            });
    }
}
