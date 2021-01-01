<?php
declare(strict_types = 1);

namespace app\Repository\Persistence;

use app\Entity\Persistence;
use app\Entity\User;

interface PersistenceRepository
{
    public function create(Persistence $persistence): void;

    public function deleteAll(): bool;

    public function findByCode(string $code): ?Persistence;

    public function findByUser(User $user): array;

    public function deleteByCode(string $code): bool;

    public function deleteByUser(User $user): bool;
}
