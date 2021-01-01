<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Control;

use app\Handlers\Admin\Control\Security\VisitHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Control\SaveSecurityRequest;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use app\Services\Settings\Settings;
use function app\permission_middleware;

class SecurityController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_CONTROL_SECURITY_ACCESS));
    }

    public function render(VisitHandler $handler): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS, $handler->handle());
    }

    public function save(SaveSecurityRequest $request, Settings $settings): JsonResponse
    {
        $settings->setArray([
            'system' => [
                'security' => [
                    'captcha' => [
                        'enabled' => (bool)$request->get('captcha_enabled'),
                        'recaptcha' => [
                            'public_key' => $request->get('recaptcha_public_key'),
                            'secret_key' => $request->get('recaptcha_secret_key')
                        ]
                    ]
                ],
            ],
            'auth' => [
                'reset_password' => [
                    'enabled' => (bool)$request->get('reset_password_enabled')
                ],
                'change_password' => [
                    'enabled' => (bool)$request->get('change_password_enabled')
                ],
            ]
        ]);
        $settings->save();

        return (new JsonResponse(Status::SUCCESS))
            ->addNotification(new Success(__('common.changed')));
    }
}
