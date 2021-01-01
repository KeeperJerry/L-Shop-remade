<?php
declare(strict_types = 1);

namespace app\Repository\Enchantment;

use app\Entity\Enchantment;

interface EnchantmentRepository
{
    public function create(Enchantment $enchantment): void;

    public function find(int $id): ?Enchantment;

    public function findByGameId(int $gameId): ?Enchantment;

    public function findWhereIn(array $identifiers): array;

    /**
     * @return Enchantment[]
     */
    public function findAll(): array;

    public function deleteAll(): bool ;
}
