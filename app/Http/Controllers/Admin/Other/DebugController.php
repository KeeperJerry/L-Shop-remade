<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Other;

use app\Handlers\Admin\Other\SendTestEmailHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Other\SendTestEmailRequest;
use app\Services\Auth\Permissions;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;
use function app\permission_middleware;

class DebugController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_OTHER_DEBUG_ACCESS));
    }

    public function render(): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS);
    }

    public function sendEmail(SendTestEmailRequest $request, SendTestEmailHandler $handler, LoggerInterface $logger): JsonResponse
    {
        try {
            $handler->handle($request->get('email'));

            return new JsonResponse(Status::SUCCESS);
        } catch (\Exception $e) {
            $logger->error($e);

            return (new JsonResponse(Status::FAILURE, [
                'message' => $e->getMessage()
            ]))
                ->setHttpStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
