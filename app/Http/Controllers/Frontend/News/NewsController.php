<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\News;

use app\Exceptions\News\NewsNotFoundException;
use app\Handlers\Frontend\News\LoadHandler;
use app\Handlers\Frontend\News\VisitHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Frontend\Shop\News\LoadRequest;
use app\Services\DateTime\Formatting\Formatter;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class NewsController
 * Respondents for output news data.
 */
class NewsController extends Controller
{
    /**
     * Render page with full news.
     *
     * @param Request      $request
     * @param VisitHandler $handler
     * @param Formatter    $formatter
     *
     * @return JsonResponse
     */
    public function render(Request $request, VisitHandler $handler, Formatter $formatter): JsonResponse
    {
        try {
            $news = $handler->handle((int)$request->route('news'));

            return new JsonResponse(Status::SUCCESS, [
                'news' => $news,
                'formatter' => $formatter
            ]);
        } catch (NewsNotFoundException $e) {
            return (new JsonResponse('news_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Load news by portions.
     *
     * @param LoadRequest $request
     * @param LoadHandler $handler
     *
     * @return JsonResponse
     */
    public function load(LoadRequest $request, LoadHandler $handler): JsonResponse
    {
        $items = $handler->load((int) $request->get('portion'));

        return new JsonResponse(Status::SUCCESS, ['items' => $items]);
    }
}
