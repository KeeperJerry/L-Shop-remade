<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Auth;

use app\Handlers\Frontend\Auth\RegisterHandler;
use app\Http\Controllers\Controller;
use app\Http\Middleware\Auth as AuthMiddleware;
use app\Http\Middleware\Captcha as CaptchaMiddleware;
use app\Http\Requests\Frontend\Auth\RegisterRequest;
use app\Services\Auth\AccessMode;
use app\Services\Auth\Exceptions\EmailAlreadyExistsException;
use app\Services\Auth\Exceptions\UsernameAlreadyExistsException;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use app\Services\Security\Captcha\Captcha;
use app\Services\Settings\DataType;
use app\Services\Settings\Settings;
use Illuminate\Http\Response;
use function app\auth_middleware;

/**
 * Class RegisterController
 * Handles requests related to user registration.
 */
class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware(auth_middleware(AuthMiddleware::GUEST));
        $this->middleware(CaptchaMiddleware::NAME)->only('handle');
    }

    /**
     * Returns the data needed to render the page with the registration form.
     *
     * @param Settings $settings
     * @param Captcha  $captcha
     *
     * @return JsonResponse
     */
    public function render(Settings $settings, Captcha $captcha): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS, [
            'accessModeAny' => $settings->get('auth.access_mode')->getValue() === AccessMode::ANY,
            'accessModeAuth' => $settings->get('auth.access_mode')->getValue() === AccessMode::ANY,
            'captchaKey' => $settings->get('system.security.captcha.enabled')->getValue(DataType::BOOL) ? $captcha->key() : null
        ]);
    }

    /**
     * Handles a user register request.
     *
     * @param RegisterRequest $request
     * @param RegisterHandler $handler
     * @param Settings        $settings
     *
     * @return JsonResponse
     */
    public function handle(
        RegisterRequest $request,
        RegisterHandler $handler,
        Settings $settings): JsonResponse
    {
        try {
            $dto = $handler->handle(
                (string)$request->get('username'),
                (string)$request->get('email'),
                (string)$request->get('password')
            );

            if ($dto->isSuccessfully()) {
                if ($dto->isActivated()) {
                    if ($settings->get('auth.register.custom_redirect.enabled')->getValue(DataType::BOOL)) {
                        // Redirect user on custom url after success registration.
                        $data = ['redirectUrl' => $settings->get('auth.register.custom_redirect.url')->getValue()];
                    } else {
                        $data = ['redirect' => 'frontend.auth.servers'];
                    }

                    return (new JsonResponse(Status::SUCCESS, $data))
                        ->addNotification(new Success(__('msg.frontend.auth.register.success')));
                }

                return new JsonResponse(Status::SUCCESS, [
                    'redirect' => 'frontend.auth.activation.sent'
                ]);
            }

            return (new JsonResponse(Status::FAILURE))
                ->addNotification(new Error(__('msg.auth.register.fail')));

        } catch (UsernameAlreadyExistsException $e) {
            return (new JsonResponse('username_already_exists'))
                ->setHttpStatus(Response::HTTP_CONFLICT)
                ->addNotification(new Error(__('msg.frontend.auth.register.username_already_exist')));
        } catch (EmailAlreadyExistsException $e) {
            return (new JsonResponse('email_already_exists'))
                ->setHttpStatus(Response::HTTP_CONFLICT)
                ->addNotification(new Error(__('msg.frontend.auth.register.email_already_exist')));
        }
    }
}
