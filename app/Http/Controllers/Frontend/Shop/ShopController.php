<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Shop;

use app\Composers\Constructors\AdminBlockConstructor;
use app\DataTransferObjects\Frontend\Shop\Server;
use app\Handlers\Frontend\News\LoadHandler;
use app\Http\Controllers\Controller;
use app\Services\Auth\AccessMode;
use app\Services\Auth\Auth;
use app\Services\Cart\Cart;
use app\Services\Media\Character\Cloak\Accessor as CloakAccessor;
use app\Services\Media\Character\Skin\Accessor as SkinAccessor;
use app\Services\Meta\System;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use app\Services\Server\Persistence\Persistence;
use app\Services\Settings\DataType;
use app\Services\Settings\Settings;

/**
 * Class ShopController
 * Handles requests that come from the shop layer.
 */
class ShopController extends Controller
{
    /**
     * Returns the data to render the store layer.
     *
     * @param AdminBlockConstructor $adminBlockConstructor
     * @param Settings              $settings
     * @param LoadHandler           $loadHandler
     * @param Persistence           $persistence
     * @param Auth                  $auth
     * @param Cart                  $cart
     * @param SkinAccessor          $skinAccessor
     * @param CloakAccessor         $cloakAccessor
     *
     * @return JsonResponse
     */
    public function render(
        AdminBlockConstructor $adminBlockConstructor,
        Settings $settings,
        LoadHandler $loadHandler,
        Persistence $persistence,
        Auth $auth,
        Cart $cart,
        SkinAccessor $skinAccessor,
        CloakAccessor $cloakAccessor)
    {
        $server = $persistence->retrieve();
        if ($server !== null) {
            $server = new Server($server);
        }

        $character = false;
        $balance = null;
        if ($auth->check()) {
            $character = $skinAccessor->allowSet($auth->getUser()) || $cloakAccessor->allowSet($auth->getUser());
            $balance = $auth->getUser()->getBalance();
        }

        $newsEnabled = $settings->get('system.news.enabled')->getValue(DataType::BOOL);
        $news = null;
        if ($newsEnabled) {
            $news = $loadHandler->load(1);
        }

        return new JsonResponse(Status::SUCCESS, [
            'currency' => [
                'plain' => $settings->get('shop.currency.name')->getValue(),
                'html' => $settings->get('shop.currency.html')->getValue()
            ],
            'character' => $character,
            'sidebar' => [
                'admin' => $adminBlockConstructor->construct()
            ],
            'auth' => [
                'user' => [
                    'balance' => $balance
                ]
            ],
            'accessModeAny' => $settings->get('auth.access_mode')->getValue() === AccessMode::ANY,
            'cart' => [
                'amount' => $persistence->retrieve() ? count($cart->retrieveServer($persistence->retrieve())) : null
            ],
            'news' => [
                'enabled' => $newsEnabled,
                'portion' => $news,
            ],
            'server' => $server,
            'github' => System::githubRepositoryUrl()
        ]);
    }
}
