<?php
declare(strict_types = 1);

namespace app\Exceptions\Purchase;

use app\Entity\Product;
use app\Exceptions\DomainException;
use Throwable;

class InvalidAmountException extends DomainException
{
    public function __construct($amount, Product $product, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Amount {$amount} is invalid for product {$product}", $code, $previous);
    }
}
