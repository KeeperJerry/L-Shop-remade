<?php
declare(strict_types = 1);

namespace app\Repository\News;

use app\Entity\News;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NewsRepository
{
    public function create(News $news): void;

    public function update(News $news): void;

    public function remove(News $news): void;

    public function deleteAll(): bool;

    public function find(int $id): ?News;

    public function findPaginatedOrderByCreatedAtDesc(int $page, int $perPage): LengthAwarePaginator;

    public function findPaginated(int $page, int $perPage): LengthAwarePaginator;

    public function findPaginatedWithOrder(string $orderBy, bool $descending, int $page, int $perPage): LengthAwarePaginator;

    public function findPaginateWithSearch(string $search,int $page, int $perPage): LengthAwarePaginator;

    public function findPaginatedWithOrderAndSearch(string $orderBy, bool $descending, string $search, int $page, int $perPage): LengthAwarePaginator;
}
