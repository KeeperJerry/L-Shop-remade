<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Auth;

use app\Handlers\Frontend\Auth\ResetPasswordHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Frontend\Auth\ResetPasswordRequest;
use app\Services\Auth\AccessMode;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Success;
use app\Services\Notification\Notificator;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use app\Services\Settings\Settings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class ResetPasswordController
 * Handles requests related to reset user password.
 */
class ResetPasswordController extends Controller
{
    /**
     * Returns the data needed to render the page with reset password form.
     *
     * @param Request              $request
     * @param ResetPasswordHandler $handler
     * @param Settings             $settings
     *
     * @return JsonResponse
     */
    public function render(Request $request, ResetPasswordHandler $handler, Settings $settings)
    {
        if (!$handler->isValidCode($request->route('code'))) {
            return (new JsonResponse(Status::FAILURE))
                ->addNotification(new Error(__('msg.frontend.auth.password.reset.invalid_code')));
        }

        return new JsonResponse(Status::SUCCESS, [
            'accessModeAny' => $settings->get('auth.access_mode')->getValue() === AccessMode::ANY,
            'accessModeAuth' => $settings->get('auth.access_mode')->getValue() === AccessMode::ANY
        ]);
    }

    /**
     * Handles a reset user password request.
     *
     * @param ResetPasswordRequest $request
     * @param ResetPasswordHandler $handler
     * @param Notificator          $notificator
     *
     * @return JsonResponse
     */
    public function handle(ResetPasswordRequest $request, ResetPasswordHandler $handler, Notificator $notificator)
    {
        $result = $handler->handle((string)$request->route('code'), (string)$request->get('password'));
        if ($result) {
            return (new JsonResponse(Status::SUCCESS, [
                'redirect' => route('frontend.auth.login.render')
            ]))->addNotification(new Success(__('msg.frontend.auth.password.reset.success')));
        }

        return (new JsonResponse(Status::FAILURE))
            ->setHttpStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->addNotification(new Error(__('msg.frontend.auth.reset.fail')));
    }
}
