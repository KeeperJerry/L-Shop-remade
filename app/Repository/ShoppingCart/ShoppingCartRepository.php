<?php
declare(strict_types = 1);

namespace app\Repository\ShoppingCart;

use app\Entity\ShoppingCart;

interface ShoppingCartRepository
{
    public function create(ShoppingCart $shoppingCart): void;

    public function deleteAll(): bool;
}
