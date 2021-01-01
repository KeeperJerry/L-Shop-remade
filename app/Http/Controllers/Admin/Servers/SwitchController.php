<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Servers;

use app\Exceptions\Server\ServerNotFoundException;
use app\Handlers\Admin\Servers\SwitchState\DisableHandler;
use app\Handlers\Admin\Servers\SwitchState\EnableHandler;
use app\Http\Controllers\Controller;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Info;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function app\permission_middleware;

class SwitchController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::SWITCH_SERVERS_STATE));
    }

    public function enable(Request $request, EnableHandler $handler): JsonResponse
    {
        try {
            $handler->handle((int)$request->route('server'));

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Info(__('msg.admin.servers.switch.enabled')));
        } catch (ServerNotFoundException $e) {
            return (new JsonResponse('server_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.servers.switch.server_not_found')));
        }
    }

    public function disable(Request $request, DisableHandler $handler): JsonResponse
    {
        try {
            $handler->handle((int)$request->route('server'));

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Info(__('msg.admin.servers.switch.disabled')));
        } catch (ServerNotFoundException $e) {
            return (new JsonResponse('server_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.servers.switch.server_not_found')));
        }
    }
}
