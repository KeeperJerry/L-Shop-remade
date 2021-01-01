<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend;

use app\Handlers\Api\MonitoringHandler;
use app\Http\Controllers\Controller;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;

/**
 * Class MonitoringController
 * Handles requests related to online server statistics.
 */
class MonitoringController extends Controller
{
    /**
     * Processes a request for statistics on online servers.
     *
     * @param MonitoringHandler $handler
     *
     * @return JsonResponse
     */
    public function monitor(MonitoringHandler $handler): JsonResponse
    {
        $objects = $handler->handle();

        return new JsonResponse(Status::SUCCESS, [
            'monitoring' => $objects
        ]);
    }
}
