<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Auth;

use app\Handlers\Frontend\Auth\ForgotPasswordHandler;
use app\Http\Controllers\Controller;
use app\Http\Middleware\Captcha as CaptchaMiddleware;
use app\Http\Requests\Frontend\Auth\ForgotPasswordRequest;
use app\Services\Auth\AccessMode;
use app\Services\Auth\Auth;
use app\Services\Auth\Exceptions\UserDoesNotExistException;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use app\Services\Security\Captcha\Captcha;
use app\Services\Settings\DataType;
use app\Services\Settings\Settings;
use Illuminate\Http\Response;

/**
 * Class ForgotPasswordController
 * Handles requests related to forgot user password actions.
 */
class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware(CaptchaMiddleware::NAME)->only('handle');
    }

    /**
     * Returns the data needed to render the page with the request form for password recovery.
     *
     * @param Auth     $auth
     * @param Settings $settings
     * @param Captcha  $captcha
     *
     * @return JsonResponse
     */
    public function render(Auth $auth, Settings $settings, Captcha $captcha): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS, [
            'onlyForAdmins' => !$auth->check() && $settings->get('auth.access_mode')->getValue() === AccessMode::GUEST,
            'accessModeAny' => $settings->get('auth.access_mode')->getValue() === AccessMode::ANY,
            'accessModeAuth' => $settings->get('auth.access_mode')->getValue() === AccessMode::ANY,
            'captchaKey' => $settings->get('system.security.captcha.enabled')->getValue(DataType::BOOL) ? $captcha->key() : null
        ]);
    }

    /**
     * Processes the request for password recovery.
     *
     * @param ForgotPasswordRequest $request
     * @param ForgotPasswordHandler $handler
     *
     * @return JsonResponse
     */
    public function handle(ForgotPasswordRequest $request, ForgotPasswordHandler $handler): JsonResponse
    {
        try {
            $handler->handle($request->get('email'));

            return (new JsonResponse(Status::SUCCESS, [
                'redirect' => 'frontend.auth.login'
            ]))->addNotification(new Success(__('msg.frontend.auth.password.forgot.success')));
        } catch (UserDoesNotExistException $e) {
            return (new JsonResponse('user_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.frontend.auth.password.forgot.user_not_found')));
        }
    }
}
