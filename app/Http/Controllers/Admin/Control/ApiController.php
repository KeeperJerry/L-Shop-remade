<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Control;

use app\DataTransferObjects\Admin\Control\Api\Save;
use app\Handlers\Admin\Control\Api\SaveHandler;
use app\Handlers\Admin\Control\Api\VisitHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Control\SaveApiSettingsRequest;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use function app\permission_middleware;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_CONTROL_API_ACCESS));
    }

    public function render(VisitHandler $handler): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS, $handler->handle());
    }

    public function save(SaveApiSettingsRequest $request, SaveHandler $handler): JsonResponse
    {
        $dto = (new Save())
            ->setApiEnabled((bool)$request->get('enabled'))
            ->setKey($request->get('key'))
            ->setDelimiter($request->get('delimiter'))
            ->setAlgorithm($request->get('algorithm'))
            ->setApiAuthEnabled((bool)$request->get('auth_enabled'))
            ->setApiRegisterEnabled((bool)$request->get('register_enabled'))
            ->setSashok724sV3LauncherEnabled((bool)$request->get('sashok724sV3_launcher_enabled'))
            ->setSashok724sV3LauncherFormat($request->get('sashok724sV3_launcher_format'))
            ->setSashok724sV3LauncherIPs($request->get('sashok724sV3_launcher_IPs'));
        $handler->handle($dto);

        return (new JsonResponse(Status::SUCCESS))
            ->addNotification(new Success(__('common.changed')));
    }
}
