<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Shop;

use app\DataTransferObjects\Frontend\Shop\Cart\Purchase;
use app\DataTransferObjects\Frontend\Shop\Server;
use app\Exceptions\Distributor\DistributionException;
use app\Exceptions\ForbiddenException;
use app\Exceptions\Product\ProductNotFoundException;
use app\Exceptions\Server\ServerNotFoundException;
use app\Handlers\Frontend\Shop\Cart\PurchaseHandler;
use app\Handlers\Frontend\Shop\Cart\PutHandler;
use app\Handlers\Frontend\Shop\Cart\RemoveHandler;
use app\Handlers\Frontend\Shop\Cart\RenderHandler;
use app\Http\Controllers\Controller;
use app\Http\Middleware\Auth as AuthMiddleware;
use app\Http\Middleware\Captcha as CaptchaMiddleware;
use app\Http\Requests\Frontend\Shop\Cart\PurchaseRequest;
use app\Http\Requests\Frontend\Shop\Cart\PutRequest;
use app\Http\Requests\Frontend\Shop\Cart\RemoveRequest;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Info;
use app\Services\Notification\Notifications\Success;
use app\Services\Notification\Notifications\Warning;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use app\Services\Security\Captcha\Captcha;
use app\Services\Server\Persistence\Persistence;
use app\Services\Settings\DataType;
use app\Services\Settings\Settings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function app\auth_middleware;

/**
 * Class CartController
 * Handles requests from the shopping cart page.
 */
class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware(auth_middleware(AuthMiddleware::ANY));
        $this->middleware(CaptchaMiddleware::NAME)->only('purchase');
    }

    /**
     * Returns the data to render the cart page.
     *
     * @param Request      $request
     * @param RenderHandler $handler
     * @param Captcha      $captcha
     * @param Persistence  $persistence
     * @param Settings     $settings
     *
     * @return JsonResponse
     */
    public function render(Request $request, RenderHandler $handler, Captcha $captcha, Persistence $persistence, Settings $settings): JsonResponse
    {
        $server = $persistence->retrieve();
        if ($server !== null) {
            $server = new Server($server);
        }

        try {
            return new JsonResponse(Status::SUCCESS, [
                'cart' => $handler->handle((int)$request->route('server')),
                'captchaKey' => $settings->get('system.security.captcha.enabled')->getValue(DataType::BOOL) ? $captcha->key() : null,
                'currentServer' => $server
            ]);
        } catch (ForbiddenException $e) {
            return (new JsonResponse('server_disabled'))
                ->setHttpStatus(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Processes a request to add product to the cart.
     *
     * @param PutRequest  $request
     * @param PutHandler  $handler
     *
     * @return JsonResponse
     */
    public function put(PutRequest $request, PutHandler $handler): JsonResponse
    {
        try {
            $amount = $handler->handle($request->get('product'));

            return (new JsonResponse(Status::SUCCESS, [
                'amount' => $amount
            ]))
                ->addNotification(new Success(__('msg.frontend.shop.catalog.put_in_cart')));
        } catch (ProductNotFoundException $e) {
            return (new JsonResponse('product_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.frontend.shop.catalog.product_not_found')));
        } catch (ForbiddenException $e) {
            return (new JsonResponse('server_disabled'))
                ->setHttpStatus(Response::HTTP_FORBIDDEN)
                ->addNotification(new Warning(__('msg.forbidden')));
        }
    }

    /**
     * Processes a request to remove product from the cart.
     *
     * @param RemoveRequest $request
     * @param RemoveHandler $handler
     *
     * @return JsonResponse
     */
    public function remove(RemoveRequest $request, RemoveHandler $handler): JsonResponse
    {
        try {
            $handler->handle($request->get('product'));

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Info(__('msg.frontend.shop.cart.remove.success')));
        } catch (ProductNotFoundException $e) {
            return (new JsonResponse('product_does_not_exist'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.frontend.shop.cart.remove.fail')));
        } catch (ForbiddenException $e) {
            return (new JsonResponse('server_disabled'))
                ->setHttpStatus(Response::HTTP_FORBIDDEN)
                ->addNotification(new Warning(__('msg.forbidden')));
        }
    }

    /**
     * Processes a request for the purchase of products that are currently in the cart.
     *
     * @param PurchaseRequest $request
     * @param PurchaseHandler $handler
     *
     * @return JsonResponse
     */
    public function purchase(PurchaseRequest $request, PurchaseHandler $handler): JsonResponse
    {
        try {
            $dto = (new Purchase())
                ->setItems($request->get('items'))
                ->setServerId((int)$request->route('server'))
                ->setUsername($request->get('username'))
                ->setIp($request->ip());

            $result = $handler->handle($dto);

            if ($result->isQuick()) {
                return (new JsonResponse(Status::SUCCESS, [
                    'quick' => true,
                    'newBalance' => $result->getNewBalance()
                ]))
                    ->addNotification(new Success(__('msg.frontend.shop.catalog.purchase.success')));
            } else {
                return new JsonResponse(Status::SUCCESS, [
                    'quick' => false,
                    'purchaseId' => $result->getPurchaseId()
                ]);
            }
        } catch (ServerNotFoundException $e) {
            return (new JsonResponse('server_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.frontend.shop.cart.purchase.server_not_found')));
        } catch (DistributionException $e) {
            return (new JsonResponse('distribution_failed'))
                ->setHttpStatus(Response::HTTP_ACCEPTED)
                ->addNotification(new Warning(__('msg.frontend.shop.catalog.purchase.distribution_failed')));
        } catch (ForbiddenException $e) {
            return (new JsonResponse('server_disabled'))
                ->setHttpStatus(Response::HTTP_FORBIDDEN)
                ->addNotification(new Warning(__('msg.forbidden')));
        }
    }
}
