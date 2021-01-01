<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Auth;

use app\Exceptions\ForbiddenException;
use app\Handlers\Frontend\Auth\LoginHandler;
use app\Http\Controllers\Controller;
use app\Http\Middleware\Auth as AuthMiddleware;
use app\Http\Requests\Frontend\Auth\LoginRequest;
use app\Services\Auth\AccessMode;
use app\Services\Auth\Auth;
use app\Services\Auth\Exceptions\BannedException;
use app\Services\Auth\Exceptions\NotActivatedException;
use app\Services\Auth\Exceptions\ThrottlingException;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Success;
use app\Services\Notification\Notifications\Warning;
use app\Services\Notification\Notificator;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use app\Services\Settings\DataType;
use app\Services\Settings\Settings;
use app\Services\Support\Lang\Ban\BanMessage;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Response;
use function app\auth_middleware;

/**
 * Class LoginController
 * Handles requests related to user authentication.
 */
class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware(auth_middleware(AuthMiddleware::GUEST));
    }

    /**
     * Returns the data needed to render the page with the authorization form.
     *
     * @param Auth        $auth
     * @param Settings    $settings
     * @param Application $app
     *
     * @return JsonResponse
     */
    public function render(Auth $auth, Settings $settings, Application $app)
    {
        return new JsonResponse(Status::SUCCESS, [
            'onlyForAdmins' => !$auth->check() && $settings->get('auth.access_mode')->getValue() === AccessMode::GUEST,
            'accessModeAny' => $settings->get('auth.access_mode')->getValue() === AccessMode::ANY,
            'downForMaintenance' => $app->isDownForMaintenance(),
            'enabledPasswordReset' => $settings->get('auth.reset_password.enabled')->getValue(DataType::BOOL),
            'enabledRegister' => $settings->get('auth.register.enabled')->getValue(DataType::BOOL)
        ]);
    }

    /**
     * Handles a user authentication request.
     *
     * @param LoginRequest $request
     * @param LoginHandler $handler
     * @param Notificator  $notificator
     * @param BanMessage   $banMessage
     *
     * @return JsonResponse
     */
    public function handle(LoginRequest $request, LoginHandler $handler, Notificator $notificator, BanMessage $banMessage)
    {
        try {
            $dto = $handler->handle(
                (string)$request->get('username'),
                (string)$request->get('password'),
                true
            );

            if ($dto->isSuccessfully()) {
                $notificator->notify(new Success(__('msg.frontend.auth.login.welcome', [
                    'username' => $dto->getUser()->getUsername()
                ])));

                return new JsonResponse(Status::SUCCESS);
            }

            return (new JsonResponse('user_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.frontend.auth.login.invalid_credentials')));

        } catch (ForbiddenException $e) {
            return (new JsonResponse('no_rights'))
                ->setHttpStatus(Response::HTTP_FORBIDDEN)
                ->addNotification(new Warning(__('msg.no_rights')));
        } catch (NotActivatedException $e) {
            return (new JsonResponse('user_not_activated'))
                ->setHttpStatus(Response::HTTP_CONFLICT)
                ->addNotification(new Error(__('msg.frontend.auth.login.not_activated')));
        } catch (ThrottlingException $e) {
            return (new JsonResponse('too_many_attempts'))
                ->setHttpStatus(Response::HTTP_LOCKED)
                ->addNotification(new Error(__('msg.frontend.auth.login.too_many_attempts', [
                    'remaining' => $e->getCooldownRemaining()
                ])));
        } catch (BannedException $e) {
            $banMessages = $banMessage->buildMessageAuto($e->getBans());
            if (count($banMessages->getMessages()) === 0) {
                return (new JsonResponse('banned'))
                    ->setHttpStatus(Response::HTTP_CONFLICT)
                    ->addNotification(new Error($banMessages->getTitle()));
            }

            $notification = $banMessages->getTitle();
            $i = 1;
            foreach ($banMessages->getMessages() as $message) {
                $notification .= "<br>{$i}) {$message}";
                $i++;
            }

            return (new JsonResponse('banned'))
                ->setHttpStatus(Response::HTTP_CONFLICT)
                ->addNotification(new Error($notification));
        }
    }
}
