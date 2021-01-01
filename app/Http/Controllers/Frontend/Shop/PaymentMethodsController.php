<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Shop;

use app\Exceptions\ForbiddenException;
use app\Exceptions\Purchase\PurchaseNotFoundException;
use app\Handlers\Frontend\Shop\Payment\VisitHandler;
use app\Http\Controllers\Controller;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class PaymentController
 * Processes requests from the payment method selection page.
 */
class PaymentMethodsController extends Controller
{
    /**
     * Returns the data to render the payment method selection page.
     *
     * @param Request      $request
     * @param VisitHandler $handler
     *
     * @return JsonResponse
     */
    public function render(Request $request, VisitHandler $handler): JsonResponse
    {
        try {
            $payers = $handler->handle((int)$request->route('purchase'));

            return new JsonResponse(Status::SUCCESS, [
                'payers' => $payers
            ]);
        } catch (PurchaseNotFoundException $e) {
            return (new JsonResponse('purchase_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND);
        } catch (ForbiddenException $e) {
            return (new JsonResponse(Status::FORBIDDEN))
                ->setHttpStatus(Response::HTTP_NOT_FOUND);
        }
    }
}
