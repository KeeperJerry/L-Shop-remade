<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Statistic;

use app\Handlers\Admin\Statistic\Show\ProfitForMonthHandler;
use app\Handlers\Admin\Statistic\Show\PurchasesForMonthHandler;
use app\Handlers\Admin\Statistic\Show\RegisteredForMonthHandler;
use app\Handlers\Admin\Statistic\Show\VisitHandler;
use app\Http\Controllers\Controller;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    public function render(VisitHandler $handler): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS, $handler->handle());
    }

    public function profitForMonth(Request $request, ProfitForMonthHandler $handler): JsonResponse
    {
        $result = $handler->handle((int)$request->get('year'), (int)$request->get('month'));

        return new JsonResponse(Status::SUCCESS, [
            'profitForMonth' => $result
        ]);
    }

    public function purchasesForMonth(Request $request, PurchasesForMonthHandler $handler): JsonResponse
    {
        $result = $handler->handle((int)$request->get('year'), (int)$request->get('month'));

        return new JsonResponse(Status::SUCCESS, [
            'purchasesForMonth' => $result
        ]);
    }

    public function registeredForMonth(Request $request, RegisteredForMonthHandler $handler): JsonResponse
    {
        $result = $handler->handle((int)$request->get('year'), (int)$request->get('month'));

        return new JsonResponse(Status::SUCCESS, [
            'registeredForMonth' => $result
        ]);
    }
}
