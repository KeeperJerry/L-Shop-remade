<?php
declare(strict_types = 1);

namespace app\Repository\BalanceTransaction;

use app\Entity\BalanceTransaction;

interface BalanceTransactionRepository
{
    public function create(BalanceTransaction $transaction): void;

    public function deleteAll(): bool;
}
