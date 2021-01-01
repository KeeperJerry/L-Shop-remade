<?php
declare(strict_types = 1);

namespace app\Repository\Server;

use app\Entity\Server;

interface ServerRepository
{
    public function create(Server $server): void;

    public function update(Server $server): void;

    public function remove(Server $server): void;

    public function deleteAll(): bool;

    public function find(int $id): ?Server;

    /**
     * @return Server[]
     */
    public function findWithEnabledMonitoring(): array;

    /**
     * @return Server[]
     */
    public function findAll(): array;

    public function findAllWithCategories(): array;
}
