<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Profile;

use app\Handlers\Frontend\Profile\Character\ChangePasswordHandler;
use app\Handlers\Frontend\Profile\Character\ResetSessionsHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Frontend\Profile\Character\ChangePasswordRequest;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Info;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use function app\permission_middleware;

/**
 * Class SettingsController
 * Processes requests from the user's settings page.
 */
class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(permission_middleware(Permissions::PROFILE_SETTINGS_ACCESS));
    }

    /**
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS);
    }

    /**
     * Processes a request to change the user's password.
     *
     * @param ChangePasswordRequest $request
     * @param ChangePasswordHandler $handler
     *
     * @return JsonResponse
     */
    public function password(ChangePasswordRequest $request, ChangePasswordHandler $handler): JsonResponse
    {
        $password = $request->get('password');
        $handler->handle($password);

        return (new JsonResponse(Status::SUCCESS))
            ->addNotification(new Success(__('msg.frontend.profile.settings.password_change.success')));
    }

    /**
     * Handles a request to reset the login of user sessions.
     *
     * @param ResetSessionsHandler $handler
     *
     * @return JsonResponse
     */
    public function resetSessions(ResetSessionsHandler $handler): JsonResponse
    {
        $handler->handle();

        return (new JsonResponse(Status::SUCCESS))
            ->addNotification(new Info(__('msg.frontend.profile.settings.reset_sessions.success')));
    }
}
