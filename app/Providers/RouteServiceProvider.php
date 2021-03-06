<?php
declare(strict_types = 1);

namespace app\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'app\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @param Router $router
     */
    public function map(Router $router): void
    {
        $this->mapSpaRoutes($router);
        $this->mapApiRoutes($router);
        $this->mapWebRoutes($router);
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @param Router $router
     */
    protected function mapWebRoutes(Router $router): void
    {
        $router->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "spa" routes for the application.
     *
     * @param Router $router
     */
    protected function mapSpaRoutes(Router $router): void
    {
        $router->prefix('spa')
             ->middleware('spa')
             ->namespace($this->namespace)
             ->group(base_path('routes/spa.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @param Router $router
     */
    protected function mapApiRoutes(Router $router): void
    {
        $router->prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
