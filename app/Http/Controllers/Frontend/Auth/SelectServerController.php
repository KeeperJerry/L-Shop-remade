<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Auth;

use app\Handlers\Frontend\Auth\ServersHandler;
use app\Http\Controllers\Controller;
use app\Http\Middleware\Auth;
use app\Services\Auth\AccessMode;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use app\Services\Settings\Settings;
use function app\auth_middleware;

/**
 * Class SelectServerController
 * Handles requests related to select server page.
 */
class SelectServerController extends Controller
{
    public function __construct()
    {
        $this->middleware(auth_middleware(Auth::ANY));
    }

    /**
     * Returns the data needed to render the select server page.
     *
     * @param ServersHandler $handler
     * @param Settings       $settings
     *
     * @return JsonResponse
     */
    public function render(ServersHandler $handler, Settings $settings)
    {
        return new JsonResponse(Status::SUCCESS, array_merge($handler->servers()->jsonSerialize(), [
            'allowLogin' => $settings->get('auth.access_mode')->getValue() === AccessMode::ANY ||
                $settings->get('auth.access_mode')->getValue() === AccessMode::AUTH
        ]));
    }
}
