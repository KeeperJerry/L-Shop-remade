<?php
declare(strict_types = 1);

namespace app\Services\Purchasing\Distributors\ShoppingCartPipeline;

use app\Entity\ShoppingCart;

class PlayerPipe
{
    public function handle(ShoppingCart $entity, \Closure $next)
    {
        $purchase = $entity->getDistribution()->getPurchaseItem()->getPurchase();
        $player = $purchase->getUser() === null ? $purchase->getPlayer() : $purchase->getUser()->getUsername();
        $entity->setPlayer($player);

        return $next($entity);
    }
}
