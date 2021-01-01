<?php
declare(strict_types = 1);

namespace app\Repository\Ban;

use app\Entity\Ban;

interface BanRepository
{
    public function find(int $id): ?Ban;

    public function create(Ban $ban): void;

    public function remove(Ban $ban): void;

    public function deleteAll(): bool;
}
