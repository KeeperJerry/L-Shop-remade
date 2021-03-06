<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Control;

use app\Handlers\Admin\Control\Basic\VisitHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Control\SaveBasicSettingsRequest;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use app\Services\Settings\Settings;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Foundation\Application;
use function app\permission_middleware;

class BasicController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_CONTROL_BASIC_ACCESS));
    }

    public function render(VisitHandler $handler): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS, $handler->handle());
    }

    public function save(SaveBasicSettingsRequest $request, Settings $settings, Application $app, Kernel $console): JsonResponse
    {
        $settings->setArray([
            'shop' => [
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'keywords' => json_encode($request->get('keywords')),
            ],
            'auth' => [
                'access_mode' => $request->get('access_mode'),
                'register' => [
                    'enabled' => (bool)$request->get('register_enabled'),
                    'send_activation' => (bool)$request->get('send_activation_enabled'),
                    'custom_redirect' => [
                        'enabled' => (bool)$request->get('custom_redirect_enabled'),
                        'url' => $request->get('custom_redirect_url'),
                    ]
                ]
            ],
            'system' => [
                'profile' => [
                    'character' => [
                        'skin' => [
                            'enabled' => (bool)$request->get('skin_enabled'),
                            'max_file_size' => (int)$request->get('skin_max_file_size'),
                            'list' => json_encode($request->get('skin_list')),
                            'hd' => [
                                'enabled' => $request->get('skin_hd_enabled'),
                                'list' => json_encode($request->get('skin_hd_list'))
                            ]
                        ],
                        'cloak' => [
                            'enabled' => (bool)$request->get('cloak_enabled'),
                            'max_file_size' => (int)$request->get('cloak_max_file_size'),
                            'list' => json_encode($request->get('cloak_list')),
                            'hd' => [
                                'enabled' => $request->get('cloak_hd_enabled'),
                                'list' => json_encode($request->get('cloak_hd_list'))
                            ]
                        ],
                    ]
                ],
                'catalog' => [
                    'pagination' => [
                        'per_page' => (int)$request->get('catalog_per_page'),
                        'order_by' => $request->get('sort_products_by'),
                        'descending' => (bool)$request->get('sort_products_descending')
                    ]
                ],
                'news' => [
                    'enabled' => (bool)$request->get('news_enabled'),
                    'pagination' => [
                        'per_page' => (int)$request->get('news_per_portion')
                    ]
                ],
                'monitoring' => [
                    'enabled' => (bool)$request->get('monitoring_enabled'),
                    'rcon' => [
                        'timeout' => (int)$request->get('monitoring_rcon_timeout'),
                        'command' => $request->get('monitoring_rcon_command'),
                        'pattern' => $request->get('monitoring_rcon_response_pattern')
                    ]
                ]
            ]
        ]);
        $settings->save();

        if ($request->get('maintenance_mode')) {
            $console->call('down');
        } else {
            $console->call('up');
        }

        return (new JsonResponse(Status::SUCCESS))
            ->addNotification(new Success(__('common.changed')));
    }
}
