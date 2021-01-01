<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Shop\Cart;

use app\Exceptions\ForbiddenException;
use app\Exceptions\Product\ProductNotFoundException;
use app\Repository\Product\ProductRepository;
use app\Services\Auth\Auth;
use app\Services\Cart\Cart;
use app\Services\Cart\Item;
use app\Services\Server\ServerAccess;

class RemoveHandler
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

    public function __construct(Auth $auth, Cart $cart, ProductRepository $productRepository)
    {
        $this->auth = $auth;
        $this->cart = $cart;
        $this->productRepository = $productRepository;
    }

    /**
     * @param int $productId
     *
     * @throws ProductNotFoundException
     */
    public function handle(int $productId): void
    {
        $product = $this->productRepository->find($productId);
        if ($product === null) {
            throw ProductNotFoundException::byId($productId);
        }

        $server = $product->getCategory()->getServer();
        if (!ServerAccess::isUserHasAccessTo($this->auth->getUser(), $server)) {
            throw new ForbiddenException("Server {$server} is disabled and the user does not have permissions to make this action");
        }

        $this->cart->remove(new Item($product, 0));
    }
}
