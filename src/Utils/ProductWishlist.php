<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-02-21
 * Time: 10:14
 */

namespace App\Utils;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductWishlist
{
    const MAX_WISHLIST_SIZE = 5;

    private $wishlistPrefix = 'wishlist_product_';
    private $session;
    private $flashBag;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $this->flashBag = $this->session->getBag('flashes');
    }

    public function add(Product $product)
    {
        if (ProductWishlist::MAX_WISHLIST_SIZE > count($this->getWishlist())) {
            $id = $product->getId();
            $this->session->set($this->makeKey($id), $id);

            $this->flashBag->add('success', $product->getName() . ' added to wishlist');
        } else {
            $this->flashBag->add('warning', 'wishlist can\'t contain more than 5 products');
        }
    }

    public function clear()
    {
        $keys = array_keys($this->getWishlist());
        foreach ($keys as $key) {
            $this->session->remove($key);
        }
    }

    public function remove(Product $product)
    {
        $this->session->remove(
            $this->makeKey($product->getId())
        );
    }

    public function getRefererUrl(Request $request): string
    {
        return $request->headers->get('referer');
    }

    public function getIdsOnWishlist(): array
    {
        return array_values($this->getWishlist());
    }

    private function getWishlist(): array
    {
        return array_filter(
            $this->session->all(),
            function ($key) {
                return strpos($key, $this->wishlistPrefix) === 0;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Prefix id with $this->wishlistPrefix
     */
    private function makeKey(int $id): string
    {
        return $this->wishlistPrefix . $id;
    }
}
