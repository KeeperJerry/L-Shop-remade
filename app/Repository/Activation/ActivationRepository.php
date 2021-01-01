<?php
declare(strict_types = 1);

namespace app\Repository\Activation;

use app\Entity\Activation;
use app\Entity\User;

interface ActivationRepository
{
    public function create(Activation $activation): void;

    public function update(Activation $activation): void;

    public function deleteAll(): bool;

    public function findByUser(User $user): array;

    public function findByCode(string $code): ?Activation;

    public function deleteByUser(User $user): void;
}
