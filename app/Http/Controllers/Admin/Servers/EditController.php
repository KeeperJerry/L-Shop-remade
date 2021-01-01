<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Servers;

use app\DataTransferObjects\Admin\Servers\Edit\Edit;
use app\DataTransferObjects\Admin\Servers\Edit\EditedCategory;
use app\Exceptions\Category\CategoryNotFoundException;
use app\Exceptions\Distributor\DistributorNotFoundException;
use app\Exceptions\Server\ServerNotFoundException;
use app\Handlers\Admin\Servers\Edit\EditHandler;
use app\Handlers\Admin\Servers\Edit\RenderHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Servers\EditRequest;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function app\permission_middleware;

class EditController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_SERVERS_CRUD_ACCESS));
    }

    public function render(Request $request, RenderHandler $handler): JsonResponse
    {
        try {
            return new JsonResponse(Status::SUCCESS, $handler->handle((int)$request->route('server')));
        } catch (ServerNotFoundException $e) {
            return (new JsonResponse('server_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND);
        }
    }

    public function edit(EditRequest $request, EditHandler $handler): JsonResponse
    {
        $categories = [];
        foreach ($request->get('categories') as $category) {
            $newCategory = new EditedCategory($category['name']);
            if (isset($category['id'])) {
                $newCategory->setId($category['id']);
            }

            $categories[] = $newCategory;
        }

        try {
            $handler->handle(
                (new Edit((int)$request->route('server'), $request->get('name'), $request->get('distributor')))
                    ->setCategories($categories)
                    ->setIp($request->get('ip'))
                    ->setPort($request->get('port') !== null ? (int)$request->get('port') : null)
                    ->setPassword($request->get('password'))
                    ->setMonitoringEnabled((bool)$request->get('monitoring_enabled'))
                    ->setServerEnabled((bool)$request->get('server_enabled'))
                    ->setDistributor($request->get('distributor'))
            );

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Success(__('msg.admin.servers.edit.success')));
        } catch (ServerNotFoundException $e) {
            return (new JsonResponse('server_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.servers.edit.server_not_found')));
        } catch (CategoryNotFoundException $e) {
            return (new JsonResponse('server_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.servers.edit.category_not_found')));
        } catch (DistributorNotFoundException $e) {
            return (new JsonResponse('distributor_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.servers.edit.distributor_not_found')));
        }
    }
}
