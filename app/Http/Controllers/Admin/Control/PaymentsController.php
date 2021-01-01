<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Control;

use app\Handlers\Admin\Control\Payments\VisitHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Control\SavePaymentsSettingsRequest;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use app\Services\Settings\Settings;
use function app\permission_middleware;

class PaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_CONTROL_PAYMENTS_ACCESS));
    }

    public function render(VisitHandler $handler): JsonResponse
    {
        return new JsonResponse(Status::SUCCESS, $handler->handle());
    }

    public function save(SavePaymentsSettingsRequest $request, Settings $settings): JsonResponse
    {
        $settings->setArray([
            'shop' => [
                'currency' => [
                    'name' => $request->get('currency'),
                    'html' => $request->get('currency_html'),
                ]
            ],
            'purchasing' => [
                'min_fill_balance_sum' => (float)$request->get('min_fill_balance_sum'),
                'services' => [
                    'robokassa' => [
                        'enabled' => (bool)$request->get('robokassa_enabled'),
                        'login' => $request->get('robokassa_login'),
                        'payment_password' => $request->get('robokassa_payment_password'),
                        'validation_password' => $request->get('robokassa_validation_password'),
                        'algorithm' => $request->get('robokassa_algorithm'),
                        'test' => (bool)$request->get('robokassa_test'),
                    ],
                    'interkassa' => [
                        'enabled' => (bool)$request->get('interkassa_enabled'),
                        'checkout_id' => $request->get('interkassa_checkout_id'),
                        'key' => $request->get('interkassa_key'),
                        'test_key' => $request->get('interkassa_test_key'),
                        'currency' => $request->get('interkassa_currency'),
                        'algorithm' => $request->get('interkassa_algorithm'),
                        'test' => (bool)$request->get('interkassa_test')
                    ]
                ]
            ]
        ]);
        $settings->save();

        return (new JsonResponse(Status::SUCCESS))
            ->addNotification(new Success(__('common.changed')));
    }
}
