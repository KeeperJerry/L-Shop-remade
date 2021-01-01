<?php
declare(strict_types = 1);

namespace app\Repository\Distribution;

use app\Entity\Distribution;
use app\Entity\Server;
use app\Entity\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DistributionRepository
{
    public function create(Distribution $distribution): void;

    public function update(Distribution $distribution): void;

    public function remove(Distribution $distribution): void;

    public function find(int $id): ?Distribution;

    public function findByUserPaginated(User $user, int $page, int $perPage): LengthAwarePaginator;

    public function findByUserPaginatedWithOrder(User $user, int $page, string $orderBy, bool $descending, int $perPage): LengthAwarePaginator;

    public function findByUserAndServerPaginated(User $user, Server $server, int $page, int $perPage): LengthAwarePaginator;

    public function findByUserAndServerPaginatedWithOrder(User $user, Server $server, int $page, string $orderBy, bool $descending, int $perPage): LengthAwarePaginator;

    public function deleteAll(): bool;
}
