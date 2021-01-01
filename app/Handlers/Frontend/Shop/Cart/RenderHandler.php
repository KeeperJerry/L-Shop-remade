<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Shop\Cart;

use app\DataTransferObjects\Frontend\Shop\CartResult;
use app\Exceptions\ForbiddenException;
use app\Exceptions\Server\ServerNotFoundException;
use app\Repository\Server\ServerRepository;
use app\Services\Auth\Auth;
use app\Services\Cart\Cart;
use app\Services\Server\Persistence\Persistence;
use app\Services\Server\ServerAccess;

class RenderHandler
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var ServerRepository
     */
    private $serverRepository;

    /**
     * @var Persistence
     */
    private $persistence;

    public function __construct(Auth $auth, Cart $cart, ServerRepository $serverRepository, Persistence $persistence)
    {
        $this->auth = $auth;
        $this->cart = $cart;
        $this->serverRepository = $serverRepository;
        $this->persistence = $persistence;
    }

    /**
     * @param int $serverId
     *
     * @return CartResult[]
     * @throws ServerNotFoundException
     * @throws ForbiddenException
     */
    public function handle(int $serverId): array
    {
        $server = $this->serverRepository->find($serverId);
        if ($server === null) {
            throw ServerNotFoundException::byId($serverId);
        }

        if (!ServerAccess::isUserHasAccessTo($this->auth->getUser(), $server)) {
            throw new ForbiddenException("Server {$server} is disabled and the user does not have permissions to make a purchase");
        }

        $this->persistence->persist($server);

        $result = [];
        foreach ($this->cart->retrieveServer($server) as $item) {
            $result[] = new CartResult($item);
        }

        return $result;
    }
}
