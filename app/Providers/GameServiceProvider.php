<?php
declare(strict_types = 1);

namespace app\Providers;

use app\Services\Game\Colorizers\Colorizer;
use app\Services\Game\Colorizers\HtmlColorizer;
use app\Services\Game\Permissions\LuckPerms\Storage as LuckPermsStorage;
use app\Services\Game\Permissions\Storage;
use Illuminate\Support\ServiceProvider;

class GameServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(Colorizer::class, HtmlColorizer::class);

        $this->app->singleton(Storage::class, LuckPermsStorage::class);
    }
}
