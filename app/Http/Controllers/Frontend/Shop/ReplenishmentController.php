<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Shop;

use app\Handlers\Frontend\Shop\ReplenishmentHandler;
use app\Http\Controllers\Controller;
use app\Http\Middleware\Captcha as CaptchaMiddleware;
use app\Http\Requests\Frontend\Shop\BalanceReplenishmentRequest;
use app\Services\Auth\AccessMode;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use app\Services\Security\Captcha\Captcha;
use app\Services\Settings\DataType;
use app\Services\Settings\Settings;
use function app\auth_middleware;

class ReplenishmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(auth_middleware(AccessMode::AUTH));
        $this->middleware(CaptchaMiddleware::NAME)->only('handle');
    }

    public function render(Settings $settings, Captcha $captcha)
    {
        return new JsonResponse(Status::SUCCESS, [
            'captchaKey' => $settings->get('system.security.captcha.enabled')->getValue(DataType::BOOL) ? $captcha->key() : null
        ]);
    }

    public function handle(BalanceReplenishmentRequest $request, ReplenishmentHandler $handler): JsonResponse
    {
        $purchaseId = $handler->handle((float)$request->get('sum'), $request->ip());

        return new JsonResponse(Status::SUCCESS, [
            'purchaseId' => $purchaseId
        ]);
    }
}
