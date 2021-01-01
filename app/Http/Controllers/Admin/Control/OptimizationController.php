<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Control;

use app\Handlers\Admin\Control\Optimization\ResetAppCacheHandler;
use app\Handlers\Admin\Control\Optimization\VisitHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Control\SaveOptimizationRequest;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use app\Services\Settings\Settings;
use function app\permission_middleware;

class OptimizationController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_CONTROL_OPTIMIZATION_ACCESS));
    }

    public function render(VisitHandler $handler): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS, $handler->handle());
    }

    public function resetAppCache(ResetAppCacheHandler $handler): JsonResponse
    {
        $handler->handle();

        return (new JsonResponse(Status::SUCCESS))
            ->addNotification(new Success(__('msg.admin.control.optimization.reset_app_cache_successfully')));
    }

    public function save(SaveOptimizationRequest $request, Settings $settings): JsonResponse
    {
        $settings->setArray([
            'system' => [
                'monitoring' => [
                    'rcon' => [
                        'ttl' => (int)$request->get('monitoring_ttl')
                    ]
                ]
            ]
        ]);
        $settings->save();

        return (new JsonResponse(Status::SUCCESS))
            ->addNotification(new Success(__('common.changed')));
    }
}
