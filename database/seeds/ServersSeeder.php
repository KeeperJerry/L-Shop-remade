<?php
declare(strict_types = 1);

use app\Entity\Category;
use app\Entity\Server;
use app\Repository\Category\CategoryRepository;
use app\Repository\Product\ProductRepository;
use app\Repository\Server\ServerRepository;
use app\Services\Purchasing\Distributors\ShoppingCartDistributor;
use Illuminate\Database\Seeder;

class ServersSeeder extends Seeder
{
    public function run(
        ServerRepository $serverRepository,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository): void
    {
        $productRepository->deleteAll();
        $categoryRepository->deleteAll();
        $serverRepository->deleteAll();

        $serverMMO = (new Server('MMO', ShoppingCartDistributor::class))
            ->setIp('127.0.0.1')
            ->setPort(25575)
            ->setPassword('123456')
            ->setEnabled(true)
            ->setMonitoringEnabled(true);
        $categoryRepository->create(new Category(__('seeding.categories.1'), $serverMMO));
        $categoryRepository->create(new Category(__('seeding.categories.2'), $serverMMO));
        $categoryRepository->create(new Category(__('seeding.categories.3'), $serverMMO));

        $serverHiTechPvP = (new Server('Hi-Tech (PvP)', ShoppingCartDistributor::class))
            ->setEnabled(true)
            ->setMonitoringEnabled(false);
        $categoryRepository->create(new Category(__('seeding.categories.4'), $serverHiTechPvP));
        $categoryRepository->create(new Category(__('seeding.categories.5'), $serverHiTechPvP));

        $serverRepository->create(
            (new Server('Hi-Tech (PvE)', ShoppingCartDistributor::class))
                ->setEnabled(true)
                ->setMonitoringEnabled(false)
        );

        $serverRepository->create(
            (new Server('RPG', ShoppingCartDistributor::class))
                ->setEnabled(false)
                ->setMonitoringEnabled(false)
        );
    }
}
