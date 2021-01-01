<?php
declare(strict_types = 1);

namespace app\Exceptions\Purchase;

use app\Entity\Purchase;
use app\Exceptions\LogicException;
use Throwable;

class AlreadyCompletedException extends LogicException
{
    public function __construct(Purchase $purchase, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Purchase {$purchase} already completed", $code, $previous);
    }
}
