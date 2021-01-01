<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Servers;

use app\DataTransferObjects\Admin\Servers\Add\Add;
use app\Exceptions\Distributor\DistributorNotFoundException;
use app\Handlers\Admin\Servers\Add\AddHandler;
use app\Handlers\Admin\Servers\Add\RenderHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Servers\AddRequest;
use function app\permission_middleware;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Response;

class AddController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_SERVERS_CRUD_ACCESS));
    }

    public function render(RenderHandler $handler): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS, $handler->handle());
    }

    public function add(AddRequest $request, AddHandler $handler): JsonResponse
    {
        try {
            $handler->handle(
                (new Add($request->get('name'), $request->get('distributor')))
                    ->setCategories($request->get('categories'))
                    ->setIp($request->get('ip'))
                    ->setPort($request->get('port') !== null ? (int)$request->get('port') : null)
                    ->setPassword($request->get('password'))
                    ->setMonitoringEnabled((bool)$request->get('monitoring_enabled'))
                    ->setServerEnabled((bool)$request->get('server_enabled'))
                    ->setDistributor($request->get('distributor'))
            );

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Success(__('msg.admin.servers.add.success')));
        } catch (DistributorNotFoundException $e) {
            return (new JsonResponse('distributor_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Success(__('msg.admin.servers.add.distributor_not_found')));
        }
    }
}
