<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Shop\Cart;

use app\Exceptions\ForbiddenException;
use app\Exceptions\Product\ProductNotFoundException;
use app\Repository\Product\ProductRepository;
use app\Services\Auth\Auth;
use app\Services\Cart\Cart;
use app\Services\Cart\Item;
use app\Services\Server\Persistence\Persistence;
use app\Services\Server\ServerAccess;

class PutHandler
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
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var Persistence
     */
    private $persistence;

    public function __construct(Auth $auth, Cart $cart, ProductRepository $productRepository, Persistence $persistence)
    {
        $this->auth = $auth;
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->persistence = $persistence;
    }

    /**
     * @param int $productId
     *
     * @return int|null Amount of items in cart after procedure completed. Null - if server not persistent.
     * @throws ProductNotFoundException
     * @throws ForbiddenException
     */
    public function handle(int $productId): ?int
    {
        $product = $this->productRepository->find($productId);
        if ($product === null) {
            throw ProductNotFoundException::byId($productId);
        }

        $server = $product->getCategory()->getServer();
        if (!ServerAccess::isUserHasAccessTo($this->auth->getUser(), $server)) {
            throw new ForbiddenException("Server {$server} is disabled and the user does not have permissions to make this action");
        }

        $this->cart->put(new Item($product, 1));

        return $this->persistence->retrieve() ? count($this->cart->retrieveServer($this->persistence->retrieve())) : null;
    }
}
