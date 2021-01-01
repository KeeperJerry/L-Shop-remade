<?php
declare(strict_types=1);

use app\Entity\User;
use app\Repository\Activation\ActivationRepository;
use app\Repository\BalanceTransaction\BalanceTransactionRepository;
use app\Repository\Ban\BanRepository;
use app\Repository\Distribution\DistributionRepository;
use app\Repository\News\NewsRepository;
use app\Repository\Persistence\PersistenceRepository;
use app\Repository\Purchase\PurchaseRepository;
use app\Repository\Reminder\ReminderRepository;
use app\Repository\Role\RoleRepository;
use app\Repository\ShoppingCart\ShoppingCartRepository;
use app\Repository\Throttle\ThrottleRepository;
use app\Repository\User\UserRepository;
use app\Services\Auth\Auth;
use app\Services\Auth\Roles;
use app\Services\Game\Permissions\LuckPerms\Repository\Group\GroupRepository;
use app\Services\Game\Permissions\LuckPerms\Repository\GroupPermission\GroupPermissionRepository;
use app\Services\Game\Permissions\LuckPerms\Repository\Player\PlayerRepository;
use app\Services\Game\Permissions\LuckPerms\Repository\PlayerPermission\PlayerPermissionRepository;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(
        Auth $auth,
        RoleRepository $roleRepository,
        UserRepository $userRepository,
        BanRepository $banRepository,
        ActivationRepository $activationRepository,
        ReminderRepository $reminderRepository,
        PersistenceRepository $persistenceRepository,
        ThrottleRepository $throttleRepository,
        NewsRepository $newsRepository,
        BalanceTransactionRepository $balanceTransactionRepository,
        PurchaseRepository $purchaseRepository,
        DistributionRepository $distributionRepository,
        ShoppingCartRepository $shoppingCartRepository,
        PlayerRepository $lpPlayerRepository,
        PlayerPermissionRepository $lpPlayerPermissionRepository,
        GroupRepository $lpGroupRepository,
        GroupPermissionRepository $lpGroupPermissionRepository): void
    {
        $activationRepository->deleteAll();
        $reminderRepository->deleteAll();
        $persistenceRepository->deleteAll();
        $throttleRepository->deleteAll();
        $newsRepository->deleteAll();
        $banRepository->deleteAll();
        $balanceTransactionRepository->deleteAll();
        $shoppingCartRepository->deleteAll();
        $distributionRepository->deleteAll();
        $purchaseRepository->deleteAll();
        $lpPlayerPermissionRepository->deleteAll();
        $lpPlayerRepository->deleteAll();
        $lpGroupPermissionRepository->deleteAll();
        $lpGroupRepository->deleteAll();
        $userRepository->deleteAll();

        $user = $auth->register(new User('admin', 'admin@example.com', 'admin'), true);

        $adminRole = $roleRepository->findByName(Roles::ADMIN);
        $user->getRoles()->add($adminRole);
        $adminRole->addUser($user);
        $userRepository->update($user);
        $roleRepository->update($adminRole);

        $auth->register(new User('user', 'user@example.com', '123456'), true);
    }
}
