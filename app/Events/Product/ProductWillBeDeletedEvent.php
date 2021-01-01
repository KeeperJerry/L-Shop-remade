<?php
declare(strict_types = 1);

namespace app\Events\Product;

use app\Entity\Product;

class ProductWillBeDeletedEvent
{
    /**
     * @var Product
     */
    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }
}
