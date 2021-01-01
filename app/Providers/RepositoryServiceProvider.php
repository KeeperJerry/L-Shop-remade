<?php
declare(strict_types = 1);

namespace app\Providers;

use app\Entity\Activation;
use app\Entity\BalanceTransaction;
use app\Entity\Ban;
use app\Entity\Category;
use app\Entity\Distribution;
use app\Entity\Enchantment;
use app\Entity\Item;
use app\Entity\News;
use app\Entity\Page;
use app\Entity\Permission;
use app\Entity\Persistence;
use app\Entity\Product;
use app\Entity\Purchase;
use app\Entity\PurchaseItem;
use app\Entity\Reminder;
use app\Entity\Role;
use app\Entity\Server;
use app\Entity\ShoppingCart;
use app\Entity\Throttle;
use app\Entity\User;
use app\Repository\Activation\ActivationRepository;
use app\Repository\Activation\DoctrineActivationRepository;
use app\Repository\BalanceTransaction\BalanceTransactionRepository;
use app\Repository\BalanceTransaction\DoctrineBalanceTransactionRepository;
use app\Repository\Ban\BanRepository;
use app\Repository\Ban\DoctrineBanRepository;
use app\Repository\Category\CategoryRepository;
use app\Repository\Category\DoctrineCategoryRepository;
use app\Repository\Distribution\DistributionRepository;
use app\Repository\Distribution\DoctrineDistributionRepository;
use app\Repository\Enchantment\DoctrineEnchantmentRepository;
use app\Repository\Enchantment\EnchantmentRepository;
use app\Repository\Item\DoctrineItemRepository;
use app\Repository\Item\ItemRepository;
use app\Repository\News\DoctrineNewsRepository;
use app\Repository\News\NewsRepository;
use app\Repository\Page\DoctrinePageRepository;
use app\Repository\Page\PageRepository;
use app\Repository\Permission\DoctrinePermissionRepository;
use app\Repository\Permission\PermissionRepository;
use app\Repository\Persistence\DoctrinePersistenceRepository;
use app\Repository\Persistence\PersistenceRepository;
use app\Repository\Product\DoctrineProductRepository;
use app\Repository\Product\ProductRepository;
use app\Repository\Purchase\DoctrinePurchaseRepository;
use app\Repository\Purchase\PurchaseRepository;
use app\Repository\PurchaseItem\DoctrinePurchaseItemRepository;
use app\Repository\PurchaseItem\PurchaseItemRepository;
use app\Repository\Reminder\DoctrineReminderRepository;
use app\Repository\Reminder\ReminderRepository;
use app\Repository\Role\DoctrineRoleRepository;
use app\Repository\Role\RoleRepository;
use app\Repository\Server\DoctrineServerRepository;
use app\Repository\Server\ServerRepository;
use app\Repository\ShoppingCart\DoctrineShoppingCartRepository;
use app\Repository\ShoppingCart\ShoppingCartRepository;
use app\Repository\Throttle\DoctrineThrottleRepository;
use app\Repository\Throttle\ThrottleRepository;
use app\Repository\User\DoctrineUserRepository;
use app\Repository\User\UserRepository;
use app\Services\Caching\CachingOptions;
use app\Services\Game\Permissions\LuckPerms\Entity\Group;
use app\Services\Game\Permissions\LuckPerms\Entity\GroupPermission;
use app\Services\Game\Permissions\LuckPerms\Entity\Player;
use app\Services\Game\Permissions\LuckPerms\Entity\PlayerPermission;
use app\Services\Game\Permissions\LuckPerms\Repository\Group\DoctrineGroupRepository;
use app\Services\Game\Permissions\LuckPerms\Repository\Group\GroupRepository;
use app\Services\Game\Permissions\LuckPerms\Repository\GroupPermission\DoctrineGroupPermissionRepository;
use app\Services\Game\Permissions\LuckPerms\Repository\GroupPermission\GroupPermissionRepository;
use app\Services\Game\Permissions\LuckPerms\Repository\Player\DoctrinePlayerRepository;
use app\Services\Game\Permissions\LuckPerms\Repository\Player\PlayerRepository;
use app\Services\Game\Permissions\LuckPerms\Repository\PlayerPermission\DoctrinePlayerPermissionRepository;
use app\Services\Game\Permissions\LuckPerms\Repository\PlayerPermission\PlayerPermissionRepository;
use app\Services\Settings\Repository\Doctrine\DoctrineRepository;
use app\Services\Settings\Repository\Repository;
use app\Services\Settings\Setting;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $config = $this->app->make(\Illuminate\Contracts\Config\Repository::class);

        $repositories = [
            UserRepository::class => [
                'concrete' => DoctrineUserRepository::class,
                'entity' => User::class,
                'caching' => [
                    'enabled' => $config->get('cache.options.users.enabled'),
                    'lifetime' => $config->get('cache.options.users.lifetime')
                ]
            ],
            RoleRepository::class => [
                'concrete' => DoctrineRoleRepository::class,
                'entity' => Role::class,
                'caching' => [
                    'enabled' => $config->get('cache.options.roles.enabled'),
                    'lifetime' => $config->get('cache.options.roles.lifetime')
                ]
            ],
            PermissionRepository::class => [
                'concrete' => DoctrinePermissionRepository::class,
                'entity' => Permission::class,
                'caching' => [
                    'enabled' => $config->get('cache.options.permissions.enabled'),
                    'lifetime' => $config->get('cache.options.permissions.lifetime')
                ]
            ],
            PersistenceRepository::class => [
                'concrete' => DoctrinePersistenceRepository::class,
                'entity' => Persistence::class
            ],
            ActivationRepository::class => [
                'concrete' => DoctrineActivationRepository::class,
                'entity' => Activation::class
            ],
            BanRepository::class => [
                'concrete' => DoctrineBanRepository::class,
                'entity' => Ban::class
            ],
            ReminderRepository::class => [
                'concrete' => DoctrineReminderRepository::class,
                'entity' => Reminder::class
            ],
            ThrottleRepository::class => [
                'concrete' => DoctrineThrottleRepository::class,
                'entity' => Throttle::class
            ],
            ServerRepository::class => [
                'concrete' => DoctrineServerRepository::class,
                'entity' => Server::class,
                'caching' => [
                    'enabled' => $config->get('cache.options.servers.enabled'),
                    'lifetime' => $config->get('cache.options.servers.lifetime')
                ]
            ],
            CategoryRepository::class => [
                'concrete' => DoctrineCategoryRepository::class,
                'entity' => Category::class,
                'caching' => [
                    'enabled' => $config->get('cache.options.categories.enabled'),
                    'lifetime' => $config->get('cache.options.categories.lifetime')
                ]
            ],
            ItemRepository::class => [
                'concrete' => DoctrineItemRepository::class,
                'entity' => Item::class,
                'caching' => [
                    'enabled' => $config->get('cache.options.items.enabled'),
                    'lifetime' => $config->get('cache.options.items.lifetime')
                ]
            ],
            ProductRepository::class => [
                'concrete' => DoctrineProductRepository::class,
                'entity' => Product::class,
                'caching' => [
                    'enabled' => $config->get('cache.options.products.enabled'),
                    'lifetime' => $config->get('cache.options.products.lifetime')
                ]
            ],
            NewsRepository::class => [
                'concrete' => DoctrineNewsRepository::class,
                'entity' => News::class
            ],
            PageRepository::class => [
                'concrete' => DoctrinePageRepository::class,
                'entity' => Page::class,
                'caching' => [
                    'enabled' => $config->get('cache.options.pages.enabled'),
                    'lifetime' => $config->get('cache.options.pages.lifetime')
                ]
            ],
            EnchantmentRepository::class => [
                'concrete' => DoctrineEnchantmentRepository::class,
                'entity' => Enchantment::class
            ],
            PurchaseRepository::class => [
                'concrete' => DoctrinePurchaseRepository::class,
                'entity' => Purchase::class
            ],
            PurchaseItemRepository::class => [
                'concrete' => DoctrinePurchaseItemRepository::class,
                'entity' => PurchaseItem::class
            ],
            BalanceTransactionRepository::class => [
                'concrete' => DoctrineBalanceTransactionRepository::class,
                'entity' => BalanceTransaction::class
            ],
            DistributionRepository::class => [
                'concrete' => DoctrineDistributionRepository::class,
                'entity' => Distribution::class
            ],
            ShoppingCartRepository::class => [
                'concrete' => DoctrineShoppingCartRepository::class,
                'entity' => ShoppingCart::class
            ],
            Repository::class => [
                'concrete' => DoctrineRepository::class,
                'entity' => Setting::class,
                'caching' => [
                    'enabled' => $config->get('cache.options.settings.enabled'),
                    'lifetime' => $config->get('cache.options.settings.lifetime')
                ]
            ],
            GroupRepository::class => [
                'concrete' => DoctrineGroupRepository::class,
                'entity' => Group::class
            ],
            PlayerRepository::class => [
                'concrete' => DoctrinePlayerRepository::class,
                'entity' => Player::class
            ],
            GroupPermissionRepository::class => [
                'concrete' => DoctrineGroupPermissionRepository::class,
                'entity' => GroupPermission::class
            ],
            PlayerPermissionRepository::class => [
                'concrete' => DoctrinePlayerPermissionRepository::class,
                'entity' => PlayerPermission::class
            ],
        ];

        foreach ($repositories as $key => $value) {
            $this->app->when($value['concrete'])
                ->needs(EntityRepository::class)
                ->give(function () use ($value) {
                    return $this->buildEntityRepository($value['entity']);
                });
            if (isset($value['caching'])) {
                $enabled = $value['caching']['enabled'];
                $lifetime = $value['caching']['lifetime'];

                $this->app->when($value['concrete'])
                    ->needs(CachingOptions::class)
                    ->give(function () use ($enabled, $lifetime) {
                        return (new CachingOptions($enabled))
                            ->setLifetime($lifetime);
                    });
            }
            $this->app->singleton($key, $value['concrete']);
        }
    }

    private function buildEntityRepository(string $entity)
    {
        return new EntityRepository(
            $this->app->make(EntityManagerInterface::class),
            new ClassMetadata($entity)
        );
    }
}
