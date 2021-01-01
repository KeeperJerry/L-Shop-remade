<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Shop;

use app\Exceptions\Payer\InvalidPaymentDataException;
use app\Exceptions\Payer\PayerNotFoundException;
use app\Exceptions\Purchase\AlreadyCompletedException;
use app\Exceptions\Purchase\PurchaseNotFoundException;
use app\Handlers\Frontend\Shop\Payment\ResultHandler;
use app\Http\Controllers\Controller;
use app\Services\Notification\Notifications\Info;
use app\Services\Notification\Notifications\Success;
use app\Services\Notification\Notificator;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function result(Request $request, ResultHandler $handler): Response
    {
        try {
            $response = $handler->handle((string)$request->route('payer'), $request->all());

            return new Response($response);
        } catch (PayerNotFoundException $e) {
            return new Response('Payer not found', Response::HTTP_NOT_FOUND);
        } catch (InvalidPaymentDataException $e) {
            return new Response('Invalid payment data', Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (PurchaseNotFoundException $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (AlreadyCompletedException $e) {
            return new Response('Purchase already completed', Response::HTTP_CONFLICT);
        }
    }

    public function wait(Repository $config, Notificator $notificator): RedirectResponse
    {
        $notificator->notify(new Info(__('msg.frontend.shop.payment.wait')));

        return new RedirectResponse($config->get('app.url'));
    }

    public function success(Repository $config, Notificator $notificator): RedirectResponse
    {
        $notificator->notify(new Success(__('msg.frontend.shop.payment.success')));

        return new RedirectResponse($config->get('app.url'));
    }

    public function fail(Repository $config, Notificator $notificator): RedirectResponse
    {
        $notificator->notify(new Info(__('msg.frontend.shop.payment.error')));

        return new RedirectResponse($config->get('app.url'));
    }
}
