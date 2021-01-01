<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend;

use app\Exceptions\Page\PageNotFoundException;
use app\Handlers\Frontend\Shop\Page\VisitHandler;
use app\Http\Controllers\Controller;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class PageController
 * This controller works with static pages.
 */
class PageController extends Controller
{
    /**
     * Returns data to render a static page.
     *
     * @param Request      $request
     * @param VisitHandler $handler
     *
     * @return JsonResponse
     */
    public function render(Request $request, VisitHandler $handler): JsonResponse
    {
        try {
            $page = $handler->handle($request->route('url'));

            return new JsonResponse(Status::SUCCESS, $page);
        } catch (PageNotFoundException $e) {
            return (new JsonResponse('page_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND);
        }
    }
}
