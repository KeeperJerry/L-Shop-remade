<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Statistic;

use app\Exceptions\Distributor\DistributionException;
use app\Exceptions\Purchase\AlreadyCompletedException;
use app\Handlers\Admin\Statistic\Purchases\CompleteHandler;
use app\Handlers\Admin\Statistic\Purchases\PaginationHandler;
use app\Http\Controllers\Controller;
use app\Services\Auth\Permissions;
use app\Services\DateTime\Formatting\JavaScriptFormatter;
use app\Services\Notification\Notifications\Success;
use app\Services\Notification\Notifications\Warning;
use app\Services\Purchasing\ViaContext;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function app\permission_middleware;

class PurchasesController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_PURCHASES_ACCESS))
            ->only('pagination');
        $this->middleware(permission_middleware(Permissions::ALLOW_COMPLETE_PURCHASES))
            ->only('complete');
    }

    public function pagination(Request $request, PaginationHandler $handler): JsonResponse
    {
        $page = is_numeric($request->get('page')) ? (int)$request->get('page') : 1;
        $perPage = is_numeric($request->get('per_page')) ? (int)$request->get('per_page') : 25;
        $orderBy = $request->get('order_by');
        $descending = (bool)$request->get('descending');

        $dto = $handler->handle($page, $perPage, $orderBy, $descending);

        return new JsonResponse(Status::SUCCESS, $dto);
    }

    /**
     * Complete the order with the received identifier if it exists and has not yet been completed.
     *
     * @param Request         $request
     * @param CompleteHandler $handler
     *
     * @return JsonResponse
     */
    public function complete(Request $request, CompleteHandler $handler): JsonResponse
    {
        try {
            $purchase = $handler->handle((int)$request->route('purchase'));

            return (new JsonResponse(Status::SUCCESS, [
                'via' => [
                    'quick' => $purchase->getVia() === ViaContext::QUICK,
                    'byAdmin' => $purchase->getVia() === ViaContext::BY_ADMIN,
                    'value' => $purchase->getVia()
                ],
                'completedAt' => (new JavaScriptFormatter())->format($purchase->getCompletedAt())
            ]))
                ->addNotification(new Success(__('msg.admin.statistic.purchases.complete.success')));
        } catch (AlreadyCompletedException $e) {
            return (new JsonResponse('already_completed'))
                ->setHttpStatus(Response::HTTP_CONFLICT)
                ->addNotification(new Warning(__('msg.admin.statistic.purchases.complete.already_completed')));
        } catch (DistributionException $e) {
            return (new JsonResponse(Status::FAILURE))
                ->setHttpStatus(Response::HTTP_ACCEPTED)
                ->addNotification(new Warning(__('msg.frontend.profile.cart.distribution.failure')));
        }
    }
}
